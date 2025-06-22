        <footer class="container py-3 my-4">
            <ul class="nav justify-content-center border-bottom pb-3 mb-3">
                <li class="nav-item"><a href="<?= CONFIG['siteURL']; ?>/start" class="nav-link px-2 text-body-secondary">Start</a></li>
                <li class="nav-item"><a href="<?= CONFIG['siteURL']; ?>/about" class="nav-link px-2 text-body-secondary">Om</a></li>
                <li class="nav-item"><a href="<?= CONFIG['siteURL']; ?>/instructions" class="nav-link px-2 text-body-secondary">Instruktioner</a></li>
                <li class="nav-item"><a href="<?= CONFIG['siteURL']; ?>/profile" class="nav-link px-2 text-body-secondary">Profil</a></li>
                <?php if (isset($_SESSION['admin']) && !empty($_SESSION['admin']) && $_SESSION['admin'] == 1) { ?><li class="nav-item"><a href="<?= CONFIG['siteURL']; ?>/administration" class="nav-link px-2 text-body-secondary">Administration</a></li><?php } ?>
                <li class="nav-item"><a href="<?= CONFIG['siteURL']; ?>/log-out" class="nav-link px-2 text-body-secondary">Logga ut</a></li>
            </ul>
            <p class="text-center text-body-secondary">
                &copy; <?= date('Y'); ?> <?= CONFIG['companyName']; ?>. Alla rättigheter förbehållna.                
            </p>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <script src="<?= CONFIG['siteURL']; ?>/assets/js/scripts.js?v=20250313093413"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var slidesContainer = document.getElementById('slidesContainer');
                
                new Sortable(slidesContainer, {
                    animation: 150,
                    onEnd: function(evt) {
                        var items = slidesContainer.querySelectorAll('.col-3');
                        items.forEach(function(item, index) {
                            item.setAttribute('data-slide-order', index + 1);
                        });
                    }
                });
            });
            document.getElementById('slidesForm').addEventListener('submit', function() {
                var items = document.querySelectorAll('#slidesContainer .col-3');
                items.forEach(function(item) {
                    var slideId = item.getAttribute('data-slide-id');
                    var slideOrder = item.getAttribute('data-slide-order');
                    
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'order[' + slideId + ']';
                    input.value = slideOrder;
                    this.appendChild(input);
                }, this);
            });        
        </script>         
    </body>
</html>