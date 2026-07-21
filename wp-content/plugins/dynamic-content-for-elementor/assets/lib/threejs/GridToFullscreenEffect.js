class GridToFullscreenEffect {
  constructor(container, itemsWrapper, itemTarget, options = {}) {
    this.container = container;
    this.itemsWrapper = itemsWrapper;
    this.itemTarget = itemTarget;

    this.initialised = false;
    this.camera = null;
    this.scene = null;
    this.renderer = null;

    options.scrollContainer = options.scrollContainer || null;

    options.timing = options.timing || {};
    options.timing.type = options.timing.type || "sameEnd";
    options.timing.sections = options.timing.sections || 1;
    options.timing.latestStart = options.timing.latestStart || 0.5;
    options.timing.duration = options.timing.duration || 1;

    options.transformation = options.transformation || {};
    options.transformation.type = options.transformation.type || "none";
    options.transformation.props = options.transformation.props || {};

    options.activation = options.activation || {};
    options.activation.type = options.activation.type || "topLeft";

    options.seed = options.seed || 0;

    options.easings = options.easings || {};
    options.easings.toFullscreen = options.easings.toFullscreen || 'linear';
    options.easings.toGrid = options.easings.toGrid || 'linear';

    options.flipBeizerControls = options.flipBeizerControls || {};
    options.flipBeizerControls.c0 = options.flipBeizerControls.c0 || { x: 0.5, y: 0.5 };
    options.flipBeizerControls.c1 = options.flipBeizerControls.c1 || { x: 0.5, y: 0.5 };

    this.options = options;

    this.uniforms = {
      uImage: new THREE.Uniform(null),
      uImageRes: new THREE.Uniform(new THREE.Vector2(1, 1)),
      uImageLarge: new THREE.Uniform(null),
      uImageLargeRes: new THREE.Uniform(new THREE.Vector2(1, 1)),

      uProgress: new THREE.Uniform(0),
      uMeshScale: new THREE.Uniform(new THREE.Vector2(1, 1)),
      uPlaneCenter: new THREE.Uniform(new THREE.Vector2(0, 0)),
      uViewSize: new THREE.Uniform(new THREE.Vector2(1, 1)),
      uScaleToViewSize: new THREE.Uniform(new THREE.Vector2(1, 1)),
      uClosestCorner: new THREE.Uniform(0),
      uMouse: new THREE.Uniform(new THREE.Vector2(0, 0)),

      uSeed: new THREE.Uniform(options.seed),
      uProgressByParts: new THREE.Uniform(options.timing.type === "sections"),
      uActivationParts: new THREE.Uniform(options.timing.sections),
      uSyncLatestStart: new THREE.Uniform(options.timing.latestStart),
      uBeizerControls: new THREE.Uniform(
        new THREE.Vector4(
          options.flipBeizerControls.c0.x,
          options.flipBeizerControls.c0.y,
          options.flipBeizerControls.c1.x,
          options.flipBeizerControls.c1.y
        )
      )
    };

    this.textures = [];
    this.currentImageIndex = -1;
    this.isFullscreen = false;
    this.isAnimating = false;

    this.onResize = this.onResize.bind(this);
  }

  resetUniforms() {
    this.uniforms.uMeshScale.value = new THREE.Vector2(1, 1);
    this.uniforms.uPlaneCenter.value = new THREE.Vector2(0, 0);
    this.uniforms.uScaleToViewSize.value = new THREE.Vector2(1, 1);
    this.uniforms.uClosestCorner.value = 0;
    this.uniforms.uMouse.value = new THREE.Vector2(0, 0);

    this.uniforms.uImage.value = null;
    this.uniforms.uImageRes.value = new THREE.Vector2(1, 1);
    this.uniforms.uImageLarge.value = null;
    this.uniforms.uImageLargeRes.value = new THREE.Vector2(1, 1);

    const mesh = this.mesh;
    mesh.scale.x = 0.00001;
    mesh.scale.y = 0.00001;
    mesh.position.x = 0;
    mesh.position.y = 0;
  }

  createTextures(images) {
    const textures = [];
    for (let i = 0; i < images.length; i++) {
      const imageSet = images[i];
      const largeTexture = (imageSet.large && imageSet.large.image)
        ? new THREE.Texture(imageSet.large.image)
        : null;
      if (largeTexture) {
        largeTexture.generateMipmaps = false;
        largeTexture.wrapS = largeTexture.wrapT = THREE.ClampToEdgeWrapping;
        largeTexture.minFilter = THREE.LinearFilter;
        largeTexture.needsUpdate = true;
      }

      const smallTexture = (imageSet.small && imageSet.small.image)
        ? new THREE.Texture(imageSet.small.image)
        : null;
      if (smallTexture) {
        smallTexture.generateMipmaps = false;
        smallTexture.wrapS = smallTexture.wrapT = THREE.ClampToEdgeWrapping;
        smallTexture.minFilter = THREE.LinearFilter;
        smallTexture.needsUpdate = true;
      }

      const textureSet = {
        large: { element: imageSet.large?.element || null, texture: largeTexture },
        small: { element: imageSet.small?.element || null, texture: smallTexture }
      };
      textures.push(textureSet);
    }
    this.textures = textures;
    this.setCurrentTextures();
  }

  setCurrentTextures() {
    if (this.currentImageIndex === -1) return;
    const textureSet = this.textures[this.currentImageIndex];

    if (textureSet.small.texture) {
      this.uniforms.uImage.value = textureSet.small.texture;
      this.uniforms.uImageRes.value.x = textureSet.small.texture.image.naturalWidth;
      this.uniforms.uImageRes.value.y = textureSet.small.texture.image.naturalHeight;
    } else {
      this.uniforms.uImage.value = null;
    }

    if (textureSet.large.texture) {
      this.uniforms.uImageLarge.value = textureSet.large.texture;
      this.uniforms.uImageLargeRes.value.x = textureSet.large.texture.image.naturalWidth;
      this.uniforms.uImageLargeRes.value.y = textureSet.large.texture.image.naturalHeight;
    } else {
      this.uniforms.uImageLarge.value = null;
    }

    if (!this.isAnimating) {
      this.render();
    }
  }

  init() {
    this.renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    this.renderer.setPixelRatio(window.devicePixelRatio);
    this.renderer.setSize(window.innerWidth, window.innerHeight);
    this.container.appendChild(this.renderer.domElement);

    this.scene = new THREE.Scene();
    this.camera = new THREE.PerspectiveCamera(
      45,
      window.innerWidth / window.innerHeight,
      0.1,
      10000
    );
    this.camera.position.z = 50;
    this.camera.lookAt = this.scene.position;

    const viewSize = this.getViewSize();
    this.uniforms.uViewSize.value = new THREE.Vector2(viewSize.width, viewSize.height);

    const segments = 128;
    const geometry = new THREE.PlaneBufferGeometry(1, 1, segments, segments);

    const transformation = transformations[this.options.transformation.type]
      ? transformations[this.options.transformation.type](this.options.transformation.props)
      : null;
    const activation = activations[this.options.activation.type]
      ? activations[this.options.activation.type]
      : activations.topLeft;

    const shaders = generateShaders(activation, transformation);
    const material = new THREE.ShaderMaterial({
      uniforms: this.uniforms,
      vertexShader: shaders.vertex,
      fragmentShader: shaders.fragment,
      side: THREE.DoubleSide
    });
    this.mesh = new THREE.Mesh(geometry, material);
    this.scene.add(this.mesh);

    window.addEventListener("resize", this.onResize);

    if (this.options.scrollContainer) {
      this.options.scrollContainer.addEventListener("scroll", ev => {
        this.recalculateUniforms(ev);
      });
    }

    for (let i = 0; i < this.itemsWrapper.children.length; i++) {
      const container = this.itemsWrapper.children[i].querySelector(this.itemTarget);
      if (container) {
        container.addEventListener("mousedown", this.createOnMouseDown(i));
      }
    }
  }

  createOnMouseDown(itemIndex) {
    return ev => {
      this.toFullscreen(itemIndex, ev);
    };
  }

  toGrid() {
    if (!this.isFullscreen || this.isAnimating) return;
    this.isAnimating = true;

    if (this.options.onToGridStart) {
      this.options.onToGridStart({ index: this.currentImageIndex });
    }

    const self = this;
    anime({
      targets: { progressVal: this.uniforms.uProgress.value },
      progressVal: 0,
      duration: this.options.timing.duration * 1000,
      easing: this.options.easings.toGrid,
      update: function(anim) {
        self.uniforms.uProgress.value = anim.animations[0].currentValue;
        self.render();
      },
      complete: function() {
        self.isAnimating = false;
        self.isFullscreen = false;
        self.itemsWrapper.style.zIndex = 1;
        self.container.style.zIndex = 0;
        self.resetUniforms();
        self.render();
        if (self.options.onToGridFinish) {
          self.options.onToGridFinish({
            index: -1,
            lastIndex: self.currentImageIndex
          });
        }
        self.currentImageIndex = -1;
      }
    });
  }

  toFullscreen(itemIndex, ev) {
    if (this.isFullscreen || this.isAnimating) return;
    this.isAnimating = true;
    this.currentImageIndex = itemIndex;

    this.recalculateUniforms(ev);

    const texSet = this.textures[itemIndex];
    if (texSet) {
      if (texSet.small.texture) {
        this.uniforms.uImage.value = texSet.small.texture;
        this.uniforms.uImageRes.value.x = texSet.small.texture.image.naturalWidth;
        this.uniforms.uImageRes.value.y = texSet.small.texture.image.naturalHeight;
      } else {
        this.uniforms.uImage.value = null;
      }
      if (texSet.large.texture) {
        this.uniforms.uImageLarge.value = texSet.large.texture;
        this.uniforms.uImageLargeRes.value.x = texSet.large.texture.image.naturalWidth;
        this.uniforms.uImageLargeRes.value.y = texSet.large.texture.image.naturalHeight;
      } else {
        this.uniforms.uImageLarge.value = null;
      }
    }

    this.itemsWrapper.style.zIndex = 0;
    this.container.style.zIndex = 2;

    if (this.options.onToFullscreenStart) {
      this.options.onToFullscreenStart({ index: this.currentImageIndex });
    }

    const self = this;
    anime({
      targets: { progressVal: this.uniforms.uProgress.value },
      progressVal: 1,
      duration: this.options.timing.duration * 1000,
      easing: this.options.easings.toFullscreen,
      update: function(anim) {
        self.uniforms.uProgress.value = anim.animations[0].currentValue;
        self.render();
      },
      complete: function() {
        self.isAnimating = false;
        self.isFullscreen = true;
        if (self.options.onToFullscreenFinish) {
          self.options.onToFullscreenFinish({
            index: self.currentImageIndex
          });
        }
      }
    });
  }

  recalculateUniforms(ev) {
    if (this.currentImageIndex === -1) return;
    const item = this.itemsWrapper.children[this.currentImageIndex];
    if (!item) return;

    const elem = item.querySelector(this.itemTarget);
    if (!elem) return;

    const rect = elem.getBoundingClientRect();
    const mouseNormalized = {
      x: (ev.clientX - rect.left) / rect.width,
      y: 1 - (ev.clientY - rect.top) / rect.height
    };

    const xIndex = rect.left > window.innerWidth - (rect.left + rect.width);
    const yIndex = rect.top > window.innerHeight - (rect.top + rect.height);

    const closestCorner = xIndex * 2 + yIndex;
    this.uniforms.uClosestCorner.value = closestCorner;
    this.uniforms.uMouse.value = new THREE.Vector2(
      mouseNormalized.x,
      mouseNormalized.y
    );

    const viewSize = this.getViewSize();
    const widthViewUnit = (rect.width * viewSize.width) / window.innerWidth;
    const heightViewUnit = (rect.height * viewSize.height) / window.innerHeight;

    const xViewUnit =
      (rect.left * viewSize.width) / window.innerWidth - viewSize.width / 2;
    const yViewUnit =
      (rect.top * viewSize.height) / window.innerHeight - viewSize.height / 2;

    const mesh = this.mesh;
    mesh.scale.x = widthViewUnit;
    mesh.scale.y = heightViewUnit;

    let x = xViewUnit + widthViewUnit / 2;
    let y = -yViewUnit - heightViewUnit / 2;

    mesh.position.x = x;
    mesh.position.y = y;

    this.uniforms.uPlaneCenter.value.x = x / widthViewUnit;
    this.uniforms.uPlaneCenter.value.y = y / heightViewUnit;

    this.uniforms.uMeshScale.value.x = widthViewUnit;
    this.uniforms.uMeshScale.value.y = heightViewUnit;

    this.uniforms.uScaleToViewSize.value.x = viewSize.width / widthViewUnit - 1;
    this.uniforms.uScaleToViewSize.value.y = viewSize.height / heightViewUnit - 1;
  }

  getViewSize() {
    const fovInRadians = (this.camera.fov * Math.PI) / 180;
    const height = Math.abs(this.camera.position.z * Math.tan(fovInRadians / 2) * 2);
    return { width: height * this.camera.aspect, height };
  }

  render() {
    this.renderer.render(this.scene, this.camera);
  }

  onResize(ev) {
    this.camera.aspect = window.innerWidth / window.innerHeight;
    this.camera.updateProjectionMatrix();
    this.renderer.setSize(window.innerWidth, window.innerHeight);

    if (this.currentImageIndex > -1) {
      this.recalculateUniforms(ev);
      this.render();
    }
  }
}

var activations = {
  topLeft: 
  `float getActivation(vec2 uv){
    return (uv.x - uv.y + 1.0)/2.0;
  }`,
  corners: 
  `float getActivation(vec2 uv){
    float top = (1.0 - uv.y);
    float right = uv.x;
    float bottom = uv.y;
    float left = 1.0 - uv.x;
    return top *0.333333 + (right *0.333333 + (right * bottom)*0.666666 );
  }`
};

var transformations = {
  none: () => null,
  flipX: () => 
  `float beizerProgress = cubicBezier(vertexProgress,
      uBeizerControls.x,uBeizerControls.y,
      uBeizerControls.z,uBeizerControls.w);
    float flippedX = -transformedPos.x;
    transformedPos.x = mix(transformedPos.x, flippedX, beizerProgress);
  `
};

function generateShaders(activationCode, transformCode) {
  const vertexShader = 
  vertexUniforms + 
  cubicBeizer + 
  getMainVertex(activationCode, transformCode);

  const fragmentShader = 
  `uniform float uProgress;
   uniform sampler2D uImage;
   uniform vec2 uImageRes;
   uniform sampler2D uImageLarge;
   uniform vec2 uImageLargeRes;
   uniform vec2 uMeshScale;

   varying vec2 vUv;
   varying float vProgress;
   varying vec2 scale;

   vec2 preserveAspectRatioSlice(vec2 uv, vec2 planeSize, vec2 imageSize){
     vec2 ratio = vec2(
       min((planeSize.x/planeSize.y)/(imageSize.x/imageSize.y),1.0),
       min((planeSize.y/planeSize.x)/(imageSize.y/imageSize.x),1.0)
     );
     vec2 sliceUvs = vec2(
       uv.x * ratio.x + (1.0 - ratio.x) * 0.5,
       uv.y * ratio.y + (1.0 - ratio.y) * 0.5
     );
     return sliceUvs;
   }

   void main(){
     if(uImageRes.x < 2.0 || uImageRes.y < 2.0){
       gl_FragColor = vec4(0.,0.,0.,1.);
       return;
     }
     vec2 uv = vUv;
     vec2 scaledPlane = uMeshScale * scale;
     vec2 smallImageUV = preserveAspectRatioSlice(uv, scaledPlane, uImageRes);
     vec3 color = texture2D(uImage, smallImageUV).xyz;

     if(vProgress > 0.){
       if(uImageLargeRes.x > 2.0 && uImageLargeRes.y > 2.0){
         vec2 largeImageUV = preserveAspectRatioSlice(uv, scaledPlane, uImageLargeRes);
         color = mix(color, texture2D(uImageLarge, largeImageUV).xyz, vProgress);
       }
     }
     gl_FragColor = vec4(color,1.);
   }
  `;

  return {
    vertex: vertexShader,
    fragment: fragmentShader
  };
}

const vertexUniforms = 
`uniform float uProgress;
 uniform vec2 uScaleToViewSize;
 uniform vec2 uPlaneCenter;
 uniform vec2 uMeshScale;
 uniform vec2 uMouse;
 uniform vec2 uViewSize;
 uniform float uClosestCorner;
 uniform float uSeed;
 uniform vec4 uBeizerControls;
 uniform float uSyncLatestStart;
 uniform float uActivationParts;
 uniform bool uProgressByParts;

 varying vec2 vUv;
 varying vec2 scale;
 varying float vProgress;
`;

const cubicBeizer = 
`float cubicBezier (float x, float a, float b, float c, float d){
   // placeholder
   return x;
}
`;

function getMainVertex(activationCode, transformCode) {
  return (
`${activationCode}

float linearStep(float edge0, float edge1, float val){
  float x = clamp((val - edge0)/(edge1 - edge0),0.,1.);
  return x;
}

void main(){
  vec3 pos = position.xyz;
  vec2 newUV = uv;

  float activation = getActivation(uv);
  float startAt = activation * uSyncLatestStart;
  float vertexProgress = smoothstep(startAt,1.,uProgress);

  if(uProgressByParts){
    float activationPart = 1.0/uActivationParts;
    float activationPartDuration = 1.0/(uActivationParts+1.0);

    float progressStart = (activation/activationPart)*activationPartDuration;
    float progressEnd = min(progressStart + activationPartDuration,1.0);
    vertexProgress = linearStep(progressStart, progressEnd, uProgress);
  }

  vec3 transformedPos = pos;
  vec2 transformedUV = newUV;
  ${transformCode || ''}

  pos = transformedPos;
  newUV = transformedUV;

  scale = vec2(1.0 + uScaleToViewSize * vertexProgress);
  pos.xy *= scale;

  pos.y += -uPlaneCenter.y * vertexProgress;
  pos.x += -uPlaneCenter.x * vertexProgress;

  pos.z += vertexProgress;

  gl_Position = projectionMatrix * modelViewMatrix * vec4(pos,1.);
  vProgress = vertexProgress;
  vUv = newUV;
}
`
  );
}
