<?php
// fungsi untuk pengecekan status login user 
// jika user belum login, alihkan ke halaman login dan tampilkan pesan = 1
if (empty($_SESSION['user_email']) && empty($_SESSION['user_password'])) {
    echo "<script type='text/javascript'>alert('Anda harus login terlebih dahulu!');</script>
          <meta http-equiv='refresh' content='0; url=?page=home'>";
}
// jika user sudah login, maka jalankan perintah untuk ubah password
else {
    $query = mysqli_query($mysqli, "SELECT * FROM tbl_konsumen as a INNER JOIN tbl_kabkota as b INNER JOIN tbl_provinsi as c 
                                    ON a.kota=b.id_kabkota AND a.provinsi=c.id_provinsi
                                    WHERE id_konsumen='$_SESSION[id_konsumen]'")
        or die('Ada kesalahan pada query tampil data konsumen: ' . mysqli_error($mysqli));

    $data = mysqli_fetch_assoc($query);

    $id_konsumen   = $data['id_konsumen'];
    $nama_konsumen = $data['nama_konsumen'];
    $alamat        = $data['alamat'];
    $id_kabkota    = $data['id_kabkota'];
    $nama_kabkota  = $data['nama_kabkota'];
    $id_provinsi   = $data['id_provinsi'];
    $nama_provinsi = $data['nama_provinsi'];
    $kode_pos      = $data['kode_pos'];
    $telepon       = $data['telepon'];
    $email         = $data['email'];
?>
    <!-- Page Heading/Breadcrumbs -->
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">
                        <i style="margin-right:6px" class="fa fa-shopping-cart"></i>
                        Proses Order
                    </h3>
                    <ol class="breadcrumb">
                        <li><a href="?page=home">Beranda</a>
                        </li>
                        <li class="active">Proses Order</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h4>Alamat Tujuan</h3>
                                <p>
                                    <i style="margin-right:7px" class="fa fa-user"></i>
                                    <?php echo $nama_konsumen; ?>
                                </p>
                                <p>
                                    <i style="margin-right:7px" class="fa fa-map-marker"></i>
                                    <?php echo $alamat; ?>, <?php echo $nama_kabkota; ?>, <?php echo $nama_provinsi; ?>, <?php echo $kode_pos; ?>
                                </p>
                                <p>
                                    <i style="margin-right:7px" class="fa fa-phone"></i>
                                    <?php echo $telepon; ?>
                                </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Gambar</th>
                                            <th>Nama Barang</th>
                                            <th>Harga</th>
                                            <th>Jumlah Beli</th>
                                            <th>Jumlah Bayar</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $query = mysqli_query($mysqli, "SELECT * FROM tbl_transaksi_tmp as a INNER JOIN tbl_barang as b
                                                                    ON a.id_barang=b.id_barang 
                                                                    WHERE id_konsumen='$_SESSION[id_konsumen]'")
                                            or die('Ada kesalahan pada query tmp transaksi: ' . mysqli_error($mysqli));

                                        while ($data = mysqli_fetch_assoc($query)) {
                                            $id_barang    = $data['id_barang'];
                                            $jumlah_beli  = $data['jumlah_beli'];
                                            $jumlah_bayar = $data['jumlah_bayar'];
                                        ?>
                                            <tr>
                                                <td width='40' class='center'><?php echo $no; ?></td>
                                                <td width='60'><img src="images/barang/<?php echo $data['gambar']; ?>" width="150"></td>
                                                <td width='150'><?php echo $data['nama_barang']; ?></td>
                                                <td width='120'>Rp. <?php echo format_rupiah_nol($data['harga']); ?></td>
                                                <td width='100'><?php echo $jumlah_beli; ?></td>
                                                <td width='120'>Rp. <?php echo format_rupiah_nol($jumlah_bayar); ?></td>
                                            </tr>
                                        <?php
                                            $no++;
                                        }

                                        $query1 = mysqli_query($mysqli, "SELECT sum(jumlah_bayar) as total FROM tbl_transaksi_tmp
                                                                    WHERE id_konsumen='$_SESSION[id_konsumen]'")
                                            or die('Ada kesalahan pada query total bayar: ' . mysqli_error($mysqli));

                                        $data1 = mysqli_fetch_assoc($query1);
                                        $total_bayar = $data1['total'];

                                        $query2 = mysqli_query($mysqli, "SELECT * FROM tbl_biaya_kirim
                                                                    WHERE provinsi='$id_provinsi' AND kabkota='$id_kabkota'")
                                            or die('Ada kesalahan pada query biaya kirim: ' . mysqli_error($mysqli));

                                        $data2 = mysqli_fetch_assoc($query2);
                                        $biaya_kirim = $data2['biaya'];
                                        $total_pembayaran = $total_bayar + $biaya_kirim;
                                        ?>
                                        <tr>
                                            <td align="right" colspan="5"><strong>Total Harga</strong></td>
                                            <td align="right"><strong>Rp. <?php echo format_rupiah_nol($total_bayar); ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td align="right" colspan="5"><strong>Biaya Kirim</strong></td>
                                            <td align="right"><strong>Rp. <?php echo format_rupiah_nol($biaya_kirim); ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td align="right" colspan="5"><strong>Total Pembayaran</strong></td>
                                            <td align="right"><strong>Rp. <?php echo format_rupiah_nol($total_pembayaran); ?></strong></td>
                                        </tr>
                                    </tbody>
                                    <!-- SCRIPT -->

                                    <script type="text/javascript">
                                        function hitung_jumlah_angsuran() {
                                            var bil1 = <?= $total_pembayaran ?>;
                                            var bil2 = document.formAngsuran.lama_angsuran.value;
                                            if (isNaN(bil2) || bil1 == "" || bil1 == 0) {
                                                var hasil = 0;
                                            } else {
                                                if (bil1 > 75000000) {
                                                    var hasil = (bil1 - 75000000) / bil2;
                                                    var dp = 750000000;
                                                } else {
                                                    var hasil = bil1 / bil2;
                                                    var dp = 0;
                                                }
                                            };
                                            document.formAngsuran.banyak_angsuran.value = "Rp. " + (hasil);
                                            document.formAngsuran.dp.value = "Rp." + (dp);
                                        }
                                    </script>
                                    <!-- /SCRIPT -->
                                    <form method="GET" action="pages/transaksi/proses_order.php" name="formAngsuran">

                                        <tfoot>
                                            <tr>
                                                <td colspan="5"></td>
                                                <td colspan="2"><label>Lama Angsuran</label></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2"> <input type="number" class="form-control" id="lama_angsuran" min="1" name="lama_angsuran" autocomplete="off" onKeyPress="return goodchars(event,'0123456789',this)" onkeyup="hitung_jumlah_angsuran(this)" /></td>
                                            </tr>
                                            <hr>
                                            <tr>
                                                <td colspan="5"></td>
                                                <td colspan="2"><label>Banyak Angsuran</label></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2"> <input type="text" class="form-control" id="banyak_angsuran" name="banyak_angsuran" disabled="" /></td>
                                            </tr>
                                            <tr>
                                                <td colspan="5"></td>
                                                <td colspan="2"><label>Dp</label></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="2"> <input type="text" class="form-control" id="dp" name="dp" disabled="" /></td>
                                            </tr>
                                        </tfoot>
                                </table>
                            </div>
                        </div>
                    </div> <!-- /.panel -->

                    <div class="">
                        <a style="width:110px" href="?page=keranjang" class="btn btn-primary">Kembali</a>
                        &nbsp; &nbsp;

                        <input type="hidden" name="id_barang" value="<?php echo $id_barang; ?>" />
                        <input type="hidden" name="jumlah_beli" value="<?php echo $jumlah_beli; ?>" />
                        <input type="hidden" name="jumlah_bayar" value="<?php echo $jumlah_bayar; ?>" />
                        <input type="hidden" name="total_pembayaran" value="<?php echo $total_pembayaran; ?>" />
                        <button type="submit" class="btn btn-primary pull-right">Proses Order</button>

                    </div>
                    </form>
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div>
    </div>
    <!-- /.row -->
<?php
}
?>