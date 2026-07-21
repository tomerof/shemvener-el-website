(function ($) {
    var dceWidgetFileBrowser = function ($scope, $) {
        // Search functionality
        var $searchForm = $scope.find('.dce-file-search-form');
        if ($searchForm.length) {
            var $searchInput = $searchForm.find('.filetxt');
            var $fileList = $searchForm.siblings('.dce-list');
            var $resetButton = $searchForm.find('.reset');
            var isQuickSearch = $searchForm.data('quick-search') === true;

            // Quick search - filter on keyup
            if (isQuickSearch) {
                $searchInput.on('keyup', function (event) {
                    var searchValue = $(this).val().toLowerCase();
                    
                    if (searchValue.length > 2) {
                        $fileList.find('ul.dce-list').show();
                        $fileList.find('li.file').each(function () {
                            var fileText = $(this).text().toLowerCase();
                            if (fileText.indexOf(searchValue) >= 0) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    } else {
                        $fileList.find('li.file').show();
                    }
                });
            }

            // Form submit search
            $searchForm.on('submit', function (event) {
                event.preventDefault();
                var searchValue = $searchInput.val().toLowerCase();
                
                if (searchValue.length > 2) {
                    $fileList.find('ul.dce-list').show();
                    $fileList.find('li.file').each(function () {
                        var fileText = $(this).text().toLowerCase();
                        if (fileText.indexOf(searchValue) >= 0) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
                return false;
            });

            // Reset button
            $resetButton.on('click', function (event) {
                event.preventDefault();
                $fileList.find('ul.dce-list').hide();
                $fileList.find('li.file').show();
                $searchInput.val('');
            });
        }

        // Folder toggle functionality
        $scope.on('click', '.folder-dir', function (e) {
            e.preventDefault();
            $(this).siblings('ul').slideToggle();
            return false;
        });

        // Hits counter functionality
        var $downloadLinks = $scope.find('.dce-list > .file a.dce-file-download');
        if ($downloadLinks.length) {
            $downloadLinks.on('click', function (event) {
                var $link = $(this);
                var postId = $link.attr('data-post-id');
                var md5 = $link.attr('data-md5');

                if (!md5) return;

                var data = {
                    action: 'dce_file_browser_hits',
                    nonce: dce_file_browser_vars.nonce,
                    md5: md5
                };

                if (postId) {
                    data.post_id = postId;
                }

                $.post(dce_file_browser_vars.ajaxurl, data);
            });
        }
    };

    $(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-filebrowser.default', dceWidgetFileBrowser);
    });
})(jQuery);

