  </div> <!-- /.content -->
</main> <!-- /.admin-main -->
</div> <!-- /.admin-wrap -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function(){
  const sb = document.getElementById('adminSidebar');
  const ov = document.getElementById('sbOverlay');
  const btnOpen = document.getElementById('sbOpen');
  const btnClose = document.getElementById('sbClose');

  function openSb(){
    if (!sb || !ov) return;
    sb.classList.add('show');
    ov.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeSb(){
    if (!sb || !ov) return;
    sb.classList.remove('show');
    ov.classList.remove('show');
    document.body.style.overflow = '';
  }

  btnOpen && btnOpen.addEventListener('click', openSb);
  btnClose && btnClose.addEventListener('click', closeSb);
  ov && ov.addEventListener('click', closeSb);

  // klik menu auto close di mobile
  if (sb){
    sb.querySelectorAll('a.nav-link').forEach(a => {
      a.addEventListener('click', () => {
        if (window.innerWidth < 992) closeSb();
      });
    });
  }

  // ESC buat nutup
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeSb();
  });
})();
</script>

</body>
</html>
