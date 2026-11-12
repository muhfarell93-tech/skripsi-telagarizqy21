<div class="table-skeleton-overlay" role="status" aria-live="polite" aria-label="Memuat data tabel">
  <div class="table-skeleton-rows" aria-hidden="true">
    @for($row = 0; $row < 6; $row++)
      <div class="table-skeleton-row">
        <span></span><span></span><span></span><span></span><span></span>
      </div>
    @endfor
  </div>
  <span class="sr-only">Memuat data tabel…</span>
</div>
