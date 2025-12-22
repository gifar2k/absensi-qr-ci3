<h2>Admin - Users</h2>
<ul>
  <?php foreach($users as $u): ?>
    <li>
      <?= htmlspecialchars($u->nama) ?> (<?= htmlspecialchars($u->nip) ?>)
      - <a href="<?= site_url('qr/token/'.$u->id) ?>">Buat QR Link</a>
    </li>
  <?php endforeach; ?>
</ul>
