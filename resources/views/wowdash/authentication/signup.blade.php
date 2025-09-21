<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">

<x-head/>

<body class="dark:bg-neutral-800 bg-neutral-100 dark:text-white">

 

    @php
    $script = '<script>
                        // ================== Password Show Hide Js Start ==========
                        function initializePasswordToggle(toggleSelector) {
                            $(toggleSelector).on("click", function() {
                                $(this).toggleClass("ri-eye-off-line");
                                var input = $($(this).attr("data-toggle"));
                                if (input.attr("type") === "password") {
                                    input.attr("type", "text");
                                } else {
                                    input.attr("type", "password");
                                }
                            });
                        }
                        // Call the function
                        initializePasswordToggle(".toggle-password");
                        // ========================= Password Show Hide Js End ===========================
               </script>';
    @endphp

<x-script />

</body>
</html>