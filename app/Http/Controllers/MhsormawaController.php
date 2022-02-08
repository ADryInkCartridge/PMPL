<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\Mahasiswa;
use App\Models\Tahap;
use App\Models\Mhsormawa;
use App\Models\Ormawa;
use App\Models\NilaiOrmawa;
use Illuminate\Support\Facades\DB;
use Auth;

class MhsormawaController extends Controller
{
    public function editkegiatan($id){
		$Kegiatan = Kegiatan::where('id',$id)->first();
        return view('editkegiatan',['kegiatan' => $Kegiatan]);
    }

    public function tambahMhsormawa()
    {
        $userid = Auth::user()->user_id;
        $data = Ormawa::where('user_id',$userid)->get();
        $mhs = Mahasiswa::all();
        return view('tambahmhsormawa',['ormawas'=> $data,'mahasiswas' => $mhs]);
    }
    
    public function addMhsormawa(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required',
            'ormawa_id' => 'required',
        ]);
        Mhsormawa::create([
            'mahasiswa_id' => $request['mahasiswa_id'],
            'ormawa_id' => $request['ormawa_id'],
        ]);
        $keg = Kegiatan::where('id_ormawa','=',$request['ormawa_id'])->get();
            foreach ($keg as $k){
                
                NilaiOrmawa::create([
                    'id_kegiatan' => $k->id,
                    'id_mhs'=> $request['mahasiswa_id'],
                    'bn'=> 0,
                    'tn'=> 0 * $k->sn,
                ]);
            }
        return redirect()->route('tambahmhsormawa')->with('success', 'Mahasiswa Berhasil Ditambahkan');
    }

    public function editMhsormawa($id){
        $userid = Auth::user()->user_id;
        $data = Ormawa::where('user_id',$userid)->get();
        $mhs = Mahasiswa::all();
        return view('editmhsormawa',['ormawas'=> $data,'mahasiswas' => $mhs ,'id'=>$id]);
    }

    public function updateMhsormawa(Request $request){
        $request->validate([
            'id' => 'required',
            'mahasiswa_id' => 'required',
            'ormawa_id' => 'required',
        ]);
        Mhsormawa::where('id',$request->id)->update([
			'mahasiswa_id' => $request['mahasiswa_id'],
            'ormawa_id' => $request['ormawa_id'],
		]);
        return redirect()->route('listmhsormawa')->with('success', 'Mahasiswa Berhasil Diupdate');
    }
    public function deleteMhsormawa(Request $request){
        $id = $request['id'];
        $userid = Auth::user()->user_id;
        $ormawa = Ormawa::where('user_id',$userid)->first();

		if (Mhsormawa::where('id', '=', $id)->exists()) {
            $id2 = Mhsormawa::where('id',$id)->first();
            $nilai = NilaiOrmawa::where('id_mhs',$id2->mahasiswa_id)->join('kegiatan_ormawa','kegiatan_ormawa.id','=','nilai_ormawa.id_kegiatan')->where('id_ormawa',$ormawa->id)->delete();
            $Mahasiswa = Mhsormawa::where('id',$id)->delete();
            return redirect()->route('listmhsormawa')->with('success', 'Mahasiswa Berhasil Dihapus');
        }
		return redirect('listmhsormawa')->withErrors('Mahasiswa tidak ditemukan');
    }

    public function listmhsormawa(Request $request)
    {
        $userid = Auth::user()->user_id;
        if($request){
            $data = Mhsormawa::join('ormawa','in_ormawa.ormawa_id','=','ormawa.id')->join(
                'mahasiswa','mahasiswa.id','=','mahasiswa_id')->where([
                ['in_ormawa.mahasiswa_id','!=',NULL],
                ['ormawa.user_id','=',$userid],
            ])->select('mahasiswa.id as mahasiswa_id','ormawa.nama as nama_ormawa'
            ,'mahasiswa.nama as namamhs','mahasiswa.id_cerebrum as id_cerebrum','in_ormawa.id as id')->where(function ($query) use ($request) {
                $query->where('mahasiswa.nama', 'LIKE', '%' . $request->term . '%' )->orWhere('ormawa.nama', 'LIKE', '%' . $request->term . '%' );
            })->orderBy('id_cerebrum','asc')->paginate(10);
        }
        return view('listmhsormawa',['mhsormawas' => $data]);
    }

    public function deleteallmhsormawa(Request $request){
        $userid = Auth::user()->user_id;
        $ormawa = Ormawa::where('user_id',$userid)->first();
        $mhs = Mhsormawa::where('ormawa_id',$ormawa->id)->get();

        foreach($mhs as $m){
            $nilai = NilaiOrmawa::where('id_mhs',$m->mahasiswa_id)->join('kegiatan_ormawa','kegiatan_ormawa.id','=','nilai_ormawa.id_kegiatan')->where('id_ormawa',$ormawa->id)->delete();
            $Mahasiswa = Mhsormawa::where('id',$m->id)->delete();
        }
            
        return redirect('listmhsormawa')->withErrors('Mahasiswa berhasil dihapus');
    }
    
}
