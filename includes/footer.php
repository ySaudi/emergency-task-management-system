</main>
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-auto">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-right mb-4 md:mb-0">
                    <div class="flex items-center justify-center md:justify-start space-x-4 space-x-reverse mb-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                    <p class="text-lg">منسق الخطة: <?php echo COORDINATOR; ?></p>
                    <p class="text-sm text-gray-400">des: <?php echo DESIGNER; ?></p>
                </div>
                <div class="text-center md:text-left">
                    <p class="text-lg mb-2"><?php echo SITE_NAME; ?></p>
                    <p class="text-sm text-gray-400"><?php echo SITE_SUBTITLE; ?></p>
                    <p class="text-sm text-gray-400 mt-2">&copy; <?php echo date('Y'); ?> جميع الحقوق محفوظة</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.querySelector('button.md\\:hidden');
            const mobileMenu = document.querySelector('div.md\\:hidden');
            
            menuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        });

        // Flash message auto-hide
        const flashMessage = document.querySelector('[role="alert"]');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.opacity = '0';
                setTimeout(() => {
                    flashMessage.remove();
                }, 300);
            }, 5000);
        }
    </script>
</body>
</html>
