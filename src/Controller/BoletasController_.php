<?php
namespace App\Controller;

use App\Controller\AppController;
define("BOLETA_ELECTRONICA", 39);

/**
 * Boletas Controller
 *
 * @property \App\Model\Table\BoletasTable $Boletas
 *
 * @method \App\Model\Entity\Boleta[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BoletasController extends AppController
{
    public function emitir()
    {        

        $boleta = $this->Boletas->newEntity();
        $config = AppController::config();
        if ($this->request->is('post')) {
            
            // primer folio a usar para envio de set de pruebas
            $folios = [
                39 => 1 // boleta electrónica
            ];
            // caratula para el envío de los dte
            $caratula = $this->request->data["caratula"];
            $Emisor = $this->request->data["emisor"];
            $Receptor = $this->request->data["receptor"];            
            // datos de las boletas (cada elemento del arreglo $set_pruebas es una boleta)
            $set_pruebas = $this->request->data["dataPruebas"];

            //pr($set_pruebas["Encabezado"]["IdDoc"]["Folio"]);exit;
            
            //$boleta["xml"] = mb_convert_encoding($this->setBoleta($folios, $caratula, $Emisor, $Receptor, $set_pruebas)), 'ISO-8859-1');         
            $boleta["xml"] = $this->setBoleta($folios, $caratula, $Emisor, $Receptor, $set_pruebas);

            /*$iso88591_1 = utf8_decode($boleta["xml"]);
            $iso88591_2 = iconv('UTF-8', 'ISO-8859-1', $boleta["xml"]);
            $iso88591_3 = mb_convert_encoding($boleta["xml"], 'ISO-8859-1', 'UTF-8');*/
            
            $dom = new \DOMDocument;
            $dom->preserveWhiteSpace = TRUE;
            $dom->loadXML(trim($boleta["xml"]));

            //Save XML as a file
            $dom->save(ROOT . DS . 'files' . DS . 'xml' . DS . $set_pruebas[0]["Encabezado"]["IdDoc"]["Folio"] . '.xml');
            echo json_encode(array("folio" => $set_pruebas[0]["Encabezado"]["IdDoc"]["Folio"]));
            exit;
            //pr($set_pruebas[0]["Encabezado"]["IdDoc"]["Folio"]);exit;
            /*$boleta["folio"] = intval($set_pruebas[0]["Encabezado"]["IdDoc"]["Folio"]);

            $boleta = $this->Boletas->patchEntity($boleta, $this->request->getData());

            if ($this->Boletas->save($boleta)) {

                echo json_encode([
                    "message"=>"guardado correctamente", 
                    "data"=>$boleta["xml"]
                    ]
                );
                exit;
                return true;
                //$this->Flash->success(__('The boleta has been saved.'));                
                //return $this->redirect(['action' => 'index']);


            }
            $this->Flash->error(__('The boleta could not be saved. Please, try again.'));*/

        }
        $this->set(compact('boleta'));
    }

    public function setBoleta($folios, $caratula, $Emisor, $Receptor, $set_pruebas){

        $config = AppController::config();
        // Objetos de Firma y Folios

        $Firma = new \sasco\LibreDTE\FirmaElectronica($config['firma']);
        
        $Folios = [];
        $rutaXml = ROOT.DS.'files'.DS.'xml'.DS.'boletas'.DS;
        foreach ($folios as $tipo => $cantidad)
            $Folios[$tipo] = new \sasco\LibreDTE\Sii\Folios(file_get_contents($rutaXml.$tipo.'.xml'));           

        // generar cada DTE, timbrar, firmar y agregar al sobre de EnvioBOLETA
        $EnvioDTE = new \sasco\LibreDTE\Sii\EnvioDte();
        
        foreach ($set_pruebas as $documento) {
            $DTE = new \sasco\LibreDTE\Sii\Dte($documento);                        
            if (!$DTE->timbrar($Folios[$DTE->getTipo()]))
                break;
            if (!$DTE->firmar($Firma))
                break;
            $EnvioDTE->agregar($DTE);
        }

        $EnvioDTE->setFirma($Firma);
        $EnvioDTE->setCaratula($caratula);
        $EnvioDTE->generar();

        if ($EnvioDTE->schemaValidate()) {            
            return $EnvioDTE->generar();
        } else {
            // si hubo errores mostrar            
            $errorMsg = '';
            foreach (\sasco\LibreDTE\Log::readAll() as $error) {

                return $error."\n";
            }
            echo $errorMsg;
        }        
    }

    public function generaPdf(){
        // Cargar EnvioDTE y extraer arreglo con datos de carátula y DTEs
        //pr($this->request->data());exit;
        $config = AppController::config();
        $archivo = ROOT . DS . 'files' . DS . 'xml' . DS . $this->request->data["folio"] . '.xml';
 
        $EnvioDte = new \sasco\LibreDTE\Sii\EnvioDte();
        $EnvioDte->loadXML(file_get_contents($archivo));
        $Caratula = $EnvioDte->getCaratula();
        $Documentos = $EnvioDte->getDocumentos();
        // directorio temporal para guardar los PDF
        $dir = sys_get_temp_dir().'/dte_'.$Caratula['RutEmisor'].'_'.$Caratula['RutReceptor'].'_'.str_replace(['-', ':', 'T'], '', $Caratula['TmstFirmaEnv']);
        if (is_dir($dir))
            \sasco\LibreDTE\File::rmdir($dir);

        if (!mkdir($dir))
            die('No fue posible crear directorio temporal para DTEs');
        // procesar cada DTEs e ir agregándolo al PDF
        foreach ($Documentos as $DTE) {
            if (!$DTE->getDatos())
                die('No se pudieron obtener los datos del DTE');
            $pdf = new \sasco\LibreDTE\Sii\Dte\PDF\Dte(false); // =false hoja carta, =true papel contínuo (false por defecto si no se pasa)
            $pdf->setFooterText();
            $pdf->setLogo('/home/delaf/www/localhost/dev/pages/sasco/website/webroot/img/logo_mini.png'); // debe ser PNG!
            $pdf->setResolucion(['FchResol'=>$Caratula['FchResol'], 'NroResol'=>$Caratula['NroResol']]);
            //$pdf->setCedible(true);
            $pdf->agregar($DTE->getDatos(), $DTE->getTED());
            $pdf->Output($dir.'/dte_'.$Caratula['RutEmisor'].'_'.$DTE->getID().'.pdf', 'F');
        }
        // entregar archivo comprimido que incluirá cada uno de los DTEs
        \sasco\LibreDTE\File::compress($dir, ['format'=>'zip', 'delete'=>true]);
    }

    public function enviar(){
        $config = AppController::config();
        // datos del envío

        $xml = file_get_contents(ROOT . DS . 'files' . DS . 'xml' . DS .$this->request->data["folio"].'.xml');
        $RutEnvia = '13991496-1';
        $RutEmisor = '13991496-1';


        // solicitar token
        
        $token = \sasco\LibreDTE\Sii\Autenticacion::getToken($config['firma']);
        if (!$token) {
            foreach (\sasco\LibreDTE\Log::readAll() as $error)
                echo $error,"\n";
            exit;
        }

        // enviar DTE
        $result = \sasco\LibreDTE\Sii::enviar($RutEnvia, $RutEmisor, $xml, $token);

        // si hubo algún error al enviar al servidor mostrar
        if ($result===false) {
            foreach (\sasco\LibreDTE\Log::readAll() as $error)
                echo $error,"\n";
            exit;
        }

        // Mostrar resultado del envío
        if ($result->STATUS!='0') {
            foreach (\sasco\LibreDTE\Log::readAll() as $error)
                echo $error,"\n";
            exit;
        }
        echo 'DTE envíado. Track ID '.$result->TRACKID,"\n";
        exit;
    }

    public function consultar(){
        // solicitar token
        $config = AppController::config();
        // trabajar en ambiente de certificación
        // solicitar token
        $token = \sasco\LibreDTE\Sii\Autenticacion::getToken($config['firma']);
        if (!$token) {
            foreach (\sasco\LibreDTE\Log::readAll() as $error)
                echo $error,"\n";
            exit;
        }


        // consultar estado enviado

        $rut = $this->request->data["rut"];
        $dv = $this->request->data["dv"];
        $trackID = $this->request->data["trackId"];

        $estado = \sasco\LibreDTE\Sii::request('QueryEstUp', 'getEstUp', [$rut, $dv, $trackID, $token]);
        //pr($estado);exit;
        // si el estado se pudo recuperar se muestra estado y glosa
        if ($estado!==false) {
            print_r([
                'codigo' => (string)$estado->xpath('/SII:RESPUESTA/SII:RESP_HDR/ESTADO')[0],
                'glosa' => (string)$estado->xpath('/SII:RESPUESTA/SII:RESP_HDR/GLOSA')[0],
            ]);
        }

        // mostrar error si hubo
        foreach (\sasco\LibreDTE\Log::readAll() as $error)
            echo $error,"\n";
            
            
        exit;
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $boletas = $this->paginate($this->Boletas);

        $this->set(compact('boletas'));
    }

    /**
     * View method
     *
     * @param string|null $id Boleta id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $boleta = $this->Boletas->get($id, [
            'contain' => []
        ]);

        $this->set('boleta', $boleta);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $boleta = $this->Boletas->newEntity();
        if ($this->request->is('post')) {
            $boleta = $this->Boletas->patchEntity($boleta, $this->request->getData());
            if ($this->Boletas->save($boleta)) {
                $this->Flash->success(__('The boleta has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The boleta could not be saved. Please, try again.'));
        }
        $this->set(compact('boleta'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Boleta id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $boleta = $this->Boletas->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $boleta = $this->Boletas->patchEntity($boleta, $this->request->getData());
            if ($this->Boletas->save($boleta)) {
                $this->Flash->success(__('The boleta has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The boleta could not be saved. Please, try again.'));
        }
        $this->set(compact('boleta'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Boleta id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $boleta = $this->Boletas->get($id);
        if ($this->Boletas->delete($boleta)) {
            $this->Flash->success(__('The boleta has been deleted.'));
        } else {
            $this->Flash->error(__('The boleta could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
