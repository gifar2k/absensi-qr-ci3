  </div><!-- /.content -->
</main><!-- /.admin-main -->
</div><!-- /.admin-wrap -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function(){
  const sb = document.getElementById('adminSidebar');
  const ov = document.getElementById('sbOverlay');
  const btnOpen  = document.getElementById('sbOpen');      // mobile open (topbar)
  const btnClose = document.getElementById('sbClose');     // mobile close (X)
  const btnDesk  = document.getElementById('sbToggleDesk'); // desktop collapse

  function openMobile(){
    if(!sb || !ov) return;
    sb.classList.add('show');
    ov.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeMobile(){
    if(!sb || !ov) return;
    sb.classList.remove('show');
    ov.classList.remove('show');
    document.body.style.overflow = '';
  }

  function toggleDesk(){
    if(!sb) return;
    sb.classList.toggle('collapsed');
    localStorage.setItem('sb_collapsed', sb.classList.contains('collapsed') ? '1' : '0');
  }

  // Mobile handlers
  btnOpen  && btnOpen.addEventListener('click', openMobile);
  btnClose && btnClose.addEventListener('click', closeMobile);
  ov && ov.addEventListener('click', closeMobile);

  // Auto close on nav click (mobile)
  if(sb){
    sb.querySelectorAll('a.nav-link').forEach(a=>{
      a.addEventListener('click', ()=>{
        if(window.innerWidth < 992) closeMobile();
      });
    });
  }

  // ESC close (mobile)
  document.addEventListener('keydown', (e)=>{
    if(e.key === 'Escape') closeMobile();
  });

  // Desktop collapse handler
  btnDesk && btnDesk.addEventListener('click', toggleDesk);

  // Restore desktop state
  if(sb && localStorage.getItem('sb_collapsed') === '1') sb.classList.add('collapsed');
})();
</script>

</body>
</html>
