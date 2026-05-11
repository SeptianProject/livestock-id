 <div class="card-panel">
     <div class="card-panel-header" style="margin-bottom: 20px">
         <div>
             <h3>Edit Informasi Profil</h3>
             <p class="subtitle">
                 Perbarui data diri dan informasi akun Anda.
             </p>
         </div>
     </div>

     <form action="" method="POST">
         <p class="form-section-title">Data Pribadi</p>
         <div
             style="
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 14px;
                  ">
             <div>
                 <label class="form-label" for="namaDepan">Nama Depan</label>
                 <input
                     type="text"
                     id="namaDepan"
                     name="nama_depan"
                     class="form-control-custom"
                     value="Admin" />
             </div>
             <div>
                 <label class="form-label" for="namaBelakang">Nama Belakang</label>
                 <input
                     type="text"
                     id="namaBelakang"
                     name="nama_belakang"
                     class="form-control-custom"
                     value="Sistem" />
             </div>
             <div>
                 <label class="form-label" for="noHP">No. Telepon</label>
                 <input
                     type="tel"
                     id="noHP"
                     name="telepon"
                     class="form-control-custom"
                     value="+62 812-0000-0000" />
             </div>
             <div>
                 <label class="form-label" for="jabatanProfil">Jabatan</label>
                 <input
                     type="text"
                     id="jabatanProfil"
                     name="jabatan"
                     class="form-control-custom"
                     value="Administrator"
                     readonly
                     style="background: #f5f7f9; cursor: not-allowed" />
             </div>
             <div style="grid-column: 1/-1">
                 <label class="form-label" for="emailProfil">Alamat Email</label>
                 <input
                     type="email"
                     id="emailProfil"
                     name="email"
                     class="form-control-custom"
                     value="admin@livestock.id" />
             </div>
             <div style="grid-column: 1/-1">
                 <label class="form-label" for="alamat">Alamat</label>
                 <textarea
                     id="alamat"
                     name="alamat"
                     class="form-control-custom"
                     rows="2"
                     style="resize: vertical">
Jl. Peternakan No. 1, Boyolali, Jawa Tengah</textarea>
             </div>
         </div>

         <p class="form-section-title" style="margin-top: 24px">
             Biografi
         </p>
         <textarea
             name="bio"
             class="form-control-custom"
             rows="3"
             style="resize: vertical"
             placeholder="Ceritakan sedikit tentang Anda...">
Administrator sistem LivestockID yang bertanggung jawab atas pengelolaan data ternak, kandang, dan petugas.</textarea>

         <div class="form-actions" style="margin-top: 20px">
             <button type="submit" class="btn-primary-custom">
                 <i class="bi bi-check-lg"></i> Simpan Perubahan
             </button>
             <button type="reset" class="btn-secondary-custom">
                 <i class="bi bi-arrow-counterclockwise"></i> Reset
             </button>
         </div>
     </form>
 </div>