<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Clas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    // Fungsi untuk menampilkan daftar siswa
    public function index()
    {
        $siswas = User::all();
        return view('siswa.index', compact('siswas'));
    }

    // Fungsi untuk menampilkan form tambah siswa
    public function create()
    {
        $clases = Clas::all();
        return view('siswa.create', compact('clases'));
    }

    // Fungsi untuk menyimpan data siswa
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required',
            'kelas_id'       => 'required',
            'nisn'           => 'required',
            'alamat'         => 'required',
            'email'          => 'required',
            'password'       => 'required',
            'no_handphone'   => 'required',
            'photo'          => 'required',
        ]);


        //siapkan data yang akan disimpan sebagai update
        $datasiswa_store = [
            'clas_id'       => $request->kelas_id,
            'name'          => $request->name,
            'nisn'          => $request->nisn,
            'alamat'        => $request->alamat,
            'email'         => $request->email,
            'password'      => $request->password,
            'no_handphone'  => $request->no_handphone
        ];

        User::create($datasiswa_store);

        return redirect('/')->with('success', 'Data siswa berhasil ditambahkan');
    }

    // Fungsi untuk menghapus data siswa
    public function destroy($id)
    {
       //fungsi untuk delete data siswa
        $datasiswa = User::find($id);

        if ($datasiswa != null) {
            // Hapus file foto dari storage
            Storage::disk('public')->delete($datasiswa->photo);

            // Hapus data user dari database
            
            $datasiswa->delete();
        }

        return redirect('/')->with('success', 'Data siswa berhasil dihapus');
    }




    // untuk menampilkan view detail siswa
    public function show($id){
        // cari data user di database berdasarkan id
        $datauser = User::find($id);


        // cek apakah user berhasil di dapat
        if($datauser == null ) {
            return redirect('/');
        }
    
        //kembali ke halaman show dan kirimkan data user yang di ambil berdasarkan id
        return view('siswa.show', compact('datauser'));
    }

    public function edit($id){
        //siapkan data atau panggil kelas
        $clases =Clas::all();

        //ambil data user siswa di tabel user berdasar kan id
        $datauser =User::find($id);

        //cek apakah datanya ada atau tidak
        if($datauser == null){
            return redirect('/');
        }

        return view('siswa.edit',compact('clases', 'datauser'));

}

    //
    public function update(Request $request,$id){
        //validasi data
        $request->validate([
            'name'         =>'required',
            'nisn'         =>'required',
            'alamat'       =>'required',
            'email'        =>'required',
            'no_handphone' =>'required',
        ]);

       // cari apakah ada user di tabel yang akan di update cari sesuai id
        $datasiswa = user::find($id);

        //siapkan data yang akan disimpan sebagai update
        $datasiswa_update =[
            'clas_id'         =>$request->kelas_id,
            'name'            =>$request->name,
            'nisn'            =>$request->nisn,
            'alamat'          =>$request->alamat,
            'email'           =>$request->email,
            'no_handphone'    =>$request->no_handphone,
        ];

        //cek apakah user update password atau tidak
        if ($request->password  != null){
            $datasiswa_update['password'] = $request->password;
        }


        // 
        if ($request-> hasfile('photo')){

          //hapus file gambar sebelumnya
          Storage::disk('public')->delete($datasiswa->photo);
          
          //update gambar baru
          $datasiswa_update['photo'] = $request->file('photo')->store('profilesiswa','public');

        }
            
        // simpan data ke dalam database
        $datasiswa->update($datasiswa_update);

        // pindahkan user ke halaman home
        return redirect('/');
    }

}        