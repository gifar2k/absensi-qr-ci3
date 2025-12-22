(function () {
  const KEY = "ilog_absensi_client_id";
  let cid = localStorage.getItem(KEY);
  if (!cid) {
    cid = (crypto?.randomUUID ? crypto.randomUUID() : (Date.now() + "-" + Math.random().toString(16).slice(2)));
    localStorage.setItem(KEY, cid);
  }
  window.__ILOG_CLIENT_ID__ = cid;
})();
