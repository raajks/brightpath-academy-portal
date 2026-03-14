<?php
// student/includes/footer.php
?>
</div><!-- /.sp-wrapper -->
<script>
(function(){
  var side    = document.getElementById('spSide');
  var overlay = document.getElementById('spOverlay');
  var burger  = document.getElementById('spBurger');
  if (burger && side && overlay) {
    burger.addEventListener('click', function(){
      side.classList.toggle('open');
      overlay.classList.toggle('show');
    });
    overlay.addEventListener('click', function(){
      side.classList.remove('open');
      overlay.classList.remove('show');
    });
  }
})();
</script>
<?= isset($extraScript) ? "<script>$extraScript</script>" : '' ?>
</body>
</html>
