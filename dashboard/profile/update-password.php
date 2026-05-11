<div class="card-panel">
    <div class="card-panel-header" style="margin-bottom: 18px">
        <div>
            <h3>Ubah Kata Sandi</h3>
            <p class="subtitle">Gunakan sandi yang kuat dan unik.</p>
        </div>
    </div>
    <form action="" method="POST">
        <div style="display: flex; flex-direction: column; gap: 12px">
            <div>
                <label class="form-label" for="sandiLama">Kata Sandi Lama</label>
                <input
                    type="password"
                    id="sandiLama"
                    name="sandi_lama"
                    class="form-control-custom"
                    placeholder="Masukkan sandi saat ini" />
            </div>
            <div>
                <label class="form-label" for="sandiBaru">Kata Sandi Baru</label>
                <input
                    type="password"
                    id="sandiBaru"
                    name="sandi_baru"
                    class="form-control-custom"
                    placeholder="Minimal 8 karakter"
                    minlength="8" />
            </div>
            <div>
                <label class="form-label" for="konfirmasiSandi">Konfirmasi Sandi Baru</label>
                <input
                    type="password"
                    id="konfirmasiSandi"
                    name="sandi_konfirmasi"
                    class="form-control-custom"
                    placeholder="Ulangi sandi baru" />
            </div>
        </div>
        <div style="margin-top: 16px">
            <button
                type="submit"
                class="btn-primary-custom"
                style="width: 100%">
                <i class="bi bi-lock"></i> Perbarui Sandi
            </button>
        </div>
    </form>
</div>