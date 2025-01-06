<?php
namespace App\Models\Integracao;
use CodeIgniter\Model;

class PontomaisModel extends Model {

    protected $dbportal;
    protected $dbrm;
    private $log_id;
    private $now;
    private $mPortal;
    
    public function __construct()
    {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm     = db_connect('dbrm');
        $this->log_id   = session()->get('log_id');
        $this->now      = date('Y-m-d H:i:s');
        $this->mPortal  = model('PortalModel');
    }

    public function logColaborador()
    {
        $query = 
            $this->dbrm->
            table("ZCRMPORTAL_PONTOMAIS_CADASTRO")->
            select('*')->
            where('codcoligada', 1)->
            where("(pontomais_id IS NULL OR pontomais_usercriado IS NULL OR pontomais_update IS NULL)")->
            orderBy('dtcad ASC');
        
        $result = $query->get();
        return $result->getResultArray();

    }

    public function logHorario()
    {

        $query = 
            $this->dbrm->
            table("ZCRMPORTAL_PONTOMAIS_TURNO")->
            join("AHORARIO", "AHORARIO.CODIGO = ZCRMPORTAL_PONTOMAIS_TURNO.codhorario AND ZCRMPORTAL_PONTOMAIS_TURNO.codcoligada = AHORARIO.CODCOLIGADA", "left")->
            join("PFUNC", "PFUNC.CHAPA = ZCRMPORTAL_PONTOMAIS_TURNO.chapa AND PFUNC.CODCOLIGADA = ZCRMPORTAL_PONTOMAIS_TURNO.CODCOLIGADA", "left")->
            select("ZCRMPORTAL_PONTOMAIS_TURNO.*, PFUNC.NOME nome, AHORARIO.DESCRICAO deschorario, DBO.CalculoIndiceHorario(ZCRMPORTAL_PONTOMAIS_TURNO.indice, CONVERT(VARCHAR(10), ZCRMPORTAL_PONTOMAIS_TURNO.dtmudanca, 103), ZCRMPORTAL_PONTOMAIS_TURNO.codhorario, ZCRMPORTAL_PONTOMAIS_TURNO.codcoligada) indice_calculo")->
            where('ZCRMPORTAL_PONTOMAIS_TURNO.codcoligada', 1)->
            where("(ZCRMPORTAL_PONTOMAIS_TURNO.id_pontomais IS NULL OR ZCRMPORTAL_PONTOMAIS_TURNO.usucad IS NULL)")->
            orderBy('ZCRMPORTAL_PONTOMAIS_TURNO.dtcad ASC')->limit(10);
       
        $result = $query->get();
        if(!$result) return false;
        return $result->getResultArray();

    }

    public function logAfd()
    {
        
        $query =
            $this->dbportal
            ->table('zcrmportal_pontomais_log a')
            ->select('a.created_at, a.uuid, a.process, (select count(uuid) from zcrmportal_pontomais_log where uuid = a.uuid and type = 2) status')
            ->like('a.message', 'Start process')
            ->groupBy('a.created_at, a.uuid, a.process')
            ->orderBy('a.created_at DESC');
            
        $result = $query->get();
            if(!$result) return false;
            return $result->getResultArray();
    }

    public function logAfdDetais($request)
    {

        $query =
            $this
            ->dbportal
            ->table('zcrmportal_pontomais_log')
            ->select("CONCAT(CONVERT(VARCHAR, created_at, 103), ' ', CONVERT(VARCHAR, created_at, 24)) created_at, message, type")
            // ->select("created_at, replace(CAST(message AS VARCHAR(MAX)), '\\r\\n', ' ') message, type")
            ->where('uuid', $request['uuid'])
            ->orderBy('timestamp_at ASC');
        
        $result = $query->get();
        return (!$result) ? false : $result->getResultArray();

    }

    public function createLog($data)
    {
        $response = $this->dbportal
            ->table('zcrmportal_pontomais_log')
            ->insert($data);
    }


}