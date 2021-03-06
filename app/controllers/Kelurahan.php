<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Kelurahan extends CI_Controller {
	public function __construct(){
  		parent::__construct();
  		date_default_timezone_set('Asia/Jakarta');
  		$this->load->model('kelurahan_model');
 	}
	public function index(){
		$this->_content();
	}
	public function _content(){
		$isi['kelas'] = "ref_data";
		$isi['namamenu'] = "Data Wilayah";
		$isi['page'] = "kelurahan";
		$isi['link'] = 'kelurahan';
		$isi['actionhapus'] = 'hapus';
		$isi['actionedit'] = 'edit';
		$isi['halaman'] = "Data Kelurahan";
		$isi['judul'] = "Halaman Data Kelurahan";
		$isi['content'] = "kelurahan_view";
		$this->load->view("dashboard_view",$isi);
	}
	public function get_data(){
        if($this->input->is_ajax_request()){
			$aColumns = array('kecamatan','nama_provinsi','nama_kota','nama_kecamatan','nama_daerah','kecamatan');
	        $sIndexColumn = "kecamatan";
	        $sLimit = "";
	        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ){
	            $sLimit = "LIMIT ".$this->angin->anti( $_GET['iDisplayStart'] ).", ".
	                $this->angin->anti( $_GET['iDisplayLength'] );
	        }
	        $numbering = $this->angin->anti( $_GET['iDisplayStart'] );
	        $page = 1;
	        if ( isset( $_GET['iSortCol_0'] ) ){
	            $sOrder = "ORDER BY  ";
	            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ){
	                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ){
	                    $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
	                        ".$this->angin->anti( $_GET['sSortDir_'.$i] ) .", ";
	                }
	            }
	            $sOrder = substr_replace( $sOrder, "", -2 );
	            if ( $sOrder == "ORDER BY" ){
	                $sOrder = "";
	            }
	        }
	        $sWhere = "";
	        if ( $_GET['sSearch'] != "" ){
	            $sWhere = "WHERE (";
	            for ( $i=0 ; $i<count($aColumns) ; $i++ ){
	                $sWhere .= $aColumns[$i]." LIKE '%".$this->angin->anti( $_GET['sSearch'] )."%' OR ";
	            }
	            $sWhere = substr_replace( $sWhere, "", -3 );
	            $sWhere .= ')';
	        }
	        for ( $i=0 ; $i<count($aColumns) ; $i++ ){
	            if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' ){
	                if ( $sWhere == "" ){
	                    $sWhere = "WHERE ";
	                }
	                else{
	                    $sWhere .= " AND ";
	                }
	                $sWhere .= $aColumns[$i]." LIKE '%".$this->angin->anti($_GET['sSearch_'.$i])."%' ";
	            }
	        }
	        $rResult = $this->kelurahan_model->data_kelurahan($aColumns, $sWhere, $sOrder, $sLimit);
	        $iFilteredTotal = 10;
	        $rResultTotal = $this->kelurahan_model->data_kelurahan_total($sIndexColumn);
	        $iTotal = $rResultTotal->num_rows();
	        $iFilteredTotal = $iTotal;
	        $output = array(
	            "sEcho" => intval($_GET['sEcho']),
	            "iTotalRecords" => $iTotal,
	            "iTotalDisplayRecords" => $iFilteredTotal,
	            "aaData" => array()
	        );
	        foreach ($rResult->result_array() as $aRow){
	            $row = array();
	            for ( $i=0 ; $i<count($aColumns) ; $i++ ){
	                if($i < 1)
	                    $row[] = $numbering+$page.'|'.$aRow[ $aColumns[$i] ];
	                else
	                    $row[] = $aRow[ $aColumns[$i] ];
	            }
	            $page++;
	            $output['aaData'][] = $row;
	        }
	        echo json_encode( $output );
	    }else{
	    	redirect("error","refresh");
	    }
	}
	public function add(){
		$isi['kelas'] = "ref_data";
		$isi['namamenu'] = "Data Wilayah";
		$isi['page'] = "kelurahan";
		$isi['link'] = 'kelurahan';
		$isi['tombolsimpan'] = "Simpan";
		$isi['tombolbatal'] = "Batal";
		$isi['action'] = "proses_add";
		$isi['halaman'] = "Add Data Kelurahan";
		$isi['judul'] = "Halaman Add Data Kelurahan";
		$isi['option_propinsi'][''] = "Pilih Propinsi";
		$isi['option_kota'][''] = "Pilih Kab / Kota";
		$isi['option_kecamatan'][''] = "Pilih Kecamatan";
		$ckpropinsi = $this->db->get('tbl_propinsi')->result();
		if(count($ckpropinsi)>0){
			foreach ($ckpropinsi as $row) {
				$isi['option_propinsi'][$row->id] = $row->name;
			}
		}else{
			$isi['option_propinsi'][''] = "Data Propinsi Belum Tersedia";
		}
		$isi['content'] = "form_kelurahan";
		$this->load->view("dashboard_view",$isi);
	}
	public function getKabkot($kode){
        if($this->input->is_ajax_request()){
			$return = "";
			$kodex = $this->angin->anti($kode);
			$data = $this->db->query("SELECT * FROM tbl_kabupaten WHERE province_Id = '$kodex'")->result();
			if(count($data)>0){
				$return = "<option value=''class=\"form-control selectpicker\" data-size=\"100\" id=\"kelurahan\" data-parsley-required=\"true\" data-live-search=\"true\" data-style=\"btn-white\"> Pilih kelurahan </option>";
				foreach ($data as $key) {
					$return .= '<option class="form-control selectpicker" data-size="100" id="kelurahan" data-parsley-required="true" data-live-search="true" data-style="btn-white" value="' .$key->id.'">' . $key->name . '</option>';
				}
			}else{
				$return .= '<option class="form-control selectpicker" data-size="100" id="kelurahan" data-parsley-required="true" data-live-search="true" data-style="btn-white" value="">Data Kelurahan Tidak Ditemukan</option>';
			}
			print $return;
		}else{
			redirect("_404","refresh");
		}
	}
	public function getKec($kode){
        if($this->input->is_ajax_request()){
			$return = "";
			$kodex = $this->angin->anti($kode);
			$data = $this->db->query("SELECT * FROM tbl_kecamatan WHERE regency_id = '$kodex'")->result();
			if(count($data)>0){
				$return = "<option value=''class=\"form-control selectpicker\" data-size=\"100\" id=\"kecamatan\" data-parsley-required=\"true\" data-live-search=\"true\" data-style=\"btn-white\"> Pilih Kecamatan </option>";
				foreach ($data as $key) {
					$return .= '<option class="form-control selectpicker" data-size="100" id="kecamatan" data-parsley-required="true" data-live-search="true" data-style="btn-white" value="' .$key->id.'">' . $key->name . '</option>';
				}
			}else{
				$return .= '<option class="form-control selectpicker" data-size="100" id="kecamatan" data-parsley-required="true" data-live-search="true" data-style="btn-white" value="">Data Kelurahan Tidak Ditemukan</option>';
			}
			print $return;
		}else{
			redirect("_404","refresh");
		}
	}
	public function proses_add(){
		$this->form_validation->set_rules('kelurahan', 'nama kelurahan', 'htmlspecialchars|trim|required|min_length[1]|max_length[50]');
		$this->form_validation->set_rules('propinsi', 'pilih propinsi', 'htmlspecialchars|trim|required|min_length[1]|max_length[15]');
		$this->form_validation->set_rules('kota', 'pilih kab / kota', 'htmlspecialchars|trim|required|min_length[1]|max_length[15]');
		$this->form_validation->set_rules('kecamatan', 'pilih kecamatan', 'htmlspecialchars|trim|required|min_length[1]|max_length[15]');
		$this->form_validation->set_message('is_unique', '%s sudah ada sebelumnya');
		$this->form_validation->set_message('required', '%s tidak boleh kosong');
		$this->form_validation->set_message('min_length', '%s minimal %s karakter');
		$this->form_validation->set_message('max_length', '%s maximal %s karakter');
		if ($this->form_validation->run() == TRUE){
			// $kecamatan = $this->angin->anti($this->input->post('kecamatan'));
			// $kelurahan = $this->angin->anti($this->input->post('kelurahan'));
			// $simpan = array('id'=>$this->input->post('propinsi').$this->input->post('kota'),
			// 	'district_id'=>$kecamatan,
			// 	'name'=>$kelurahan);
			// $this->db->insert('tbl_kelurahan',$simpan);
			redirect('kelurahan','refresh');
        }else{
            $this->add();
        }
	}
	public function hapus($kode=Null){
        if($this->input->is_ajax_request()){
			$ckdata = $this->db->get_where('tbl_kelurahan',array('id'=>$this->angin->anti($kode)))->result();
			if(count($ckdata)>0){
				$data['say'] = "NotOk";
			}else{
				$data['say'] = "NotOk";

				// $this->db->where('Id',$kode);
				// if($this->db->delete('tbl_kelurahan')){
				// 	$data['say'] = "ok";
				// }else{
				// 	$data['say'] = "NotOk";
				// }
			}
			if('IS_AJAX'){
			    echo json_encode($data);
			}
		}else{
			redirect("error","refresh");
		}
	}
	public function cekdata($kode=Null){
        if($this->input->is_ajax_request()){
			$ckdata = $this->db->get_where('tbl_kelurahan',array('id'=>$this->angin->anti($kode)))->result();
			if(count($ckdata)>0){
				$data['say'] = "ok";
			}else{
				$data['say'] = "NotOk";
			}
			if('IS_AJAX'){
			    echo json_encode($data);
			}
		}else{
			redirect("error","refresh");
		}
	}
	public function edit($kode=Null){
		$ckdata = $this->db->get_where('view_kelurahan',array('id_kec'=>$this->angin->anti($kode)))->result();
		if(count($ckdata)>0){
			// foreach ($ckdata as $key) {
			// 	$isi['default']['kelurahan'] = $key->nama_kec;
			// 	$isi['option_propinsi'][$key->id_pro] = $key->nama_pro;
			// 	$isi['option_kota'][$key->id_kab] = $key->nama_kab;
			// }
			// $this->session->set_userdata('idna',$kode);
			// $ckpropinsi = $this->db->get('tbl_propinsi')->result();
			// if(count($ckpropinsi)>0){
			// 	foreach ($ckpropinsi as $row) {
			// 		$isi['option_propinsi'][$row->id] = $row->name;
			// 	}
			// }else{
			// 	$isi['option_propinsi'][''] = "Data Propinsi Belum Tersedia";
			// }
			// $ckkota = $this->db->get_where('tbl_kabupaten',array('province_Id'=>$key->id_pro))->result();
			// if(count($ckkota)>0){
			// 	foreach ($ckkota as $row) {
			// 		$isi['option_kota'][$row->id] = $row->name;
			// 	}
			// }else{
			// 	$isi['option_kota'][''] = "Data Kab /  Kota Tidak Ditemukan";
			// }
			// $isi['kelas'] = "ref_data";
			// $isi['namamenu'] = "Data Wilayah";
			// $isi['page'] = "kelurahan";
			// $isi['link'] = 'kelurahan';
			// $isi['tombolsimpan'] = "Edit";
			// $isi['tombolbatal'] = "Batal";
			// $isi['action'] = "../proses_edit";
			// $isi['halaman'] = "Edit Data Kelurahan";
			// $isi['judul'] = "Halaman Edit Data Kelurahan";
			// $isi['content'] = "form_kelurahan";
			// $this->load->view("dashboard_view",$isi);
			redirect('kelurahan','refresh');
		}else{
			redirect('error','refresh');
		}
	}
	public function proses_edit(){
		// $this->form_validation->set_rules('kelurahan', 'nama kelurahan', 'htmlspecialchars|trim|required|min_length[1]|max_length[15]');
		// $this->form_validation->set_rules('propinsi', 'pilih propinsi', 'htmlspecialchars|trim|required|min_length[1]|max_length[15]');
		// $this->form_validation->set_rules('kota', 'pilih kab / kota', 'htmlspecialchars|trim|required|min_length[1]|max_length[15]');
		// $this->form_validation->set_message('is_unique', '%s sudah ada sebelumnya');
		// $this->form_validation->set_message('required', '%s tidak boleh kosong');
		// $this->form_validation->set_message('min_length', '%s minimal %s karakter');
		// $this->form_validation->set_message('max_length', '%s maximal %s karakter');
		// if ($this->form_validation->run() == TRUE){
		// 	$kota = $this->angin->anti($this->input->post('kota'));
		// 	$kelurahan = $this->angin->anti($this->input->post('kelurahan'));
		// 	$simpan = array('regency_Id'=>$kota,
		// 		'name'=>$kelurahan);
		// 	$this->db->where('Id',$this->session->userdata('idna'));
		// 	$this->db->update('tbl_kelurahan',$simpan);
		// 	$this->session->unset_userdata('idna');
		// 	redirect('kelurahan','refresh');
  //       }else{
  //       	redirect('kelurahana/edit/'.$this->session->userdata('idna'),'refresh');
  //       }
		redirect("kelurahan","refresh");
	}
}
