</main>
  <footer class="site-footer">
    <div class="container">
      <p>&copy; <?= date('Y') ?> Modul Pembelajaran. Dibangun dengan PHP Native.</p>
    </div>
  </footer>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HG6GQ6fQ7vVQvQm6aQ9E6H0ub1S1KQv9K9qQG8GJ5dM" crossorigin="anonymous"></script>
  <script>
  // Scroll reveal for elements with .reveal
  (function(){
    var els = document.querySelectorAll('.reveal');
    if('IntersectionObserver' in window){
      var io = new IntersectionObserver(function(entries){
        entries.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('shown'); io.unobserve(e.target); } });
      }, {threshold:0.15});
      els.forEach(function(el){ io.observe(el); });
    } else {
      // Fallback: show immediately
      els.forEach(function(el){ el.classList.add('shown'); });
    }
  })();
  </script>
</body>
</html>