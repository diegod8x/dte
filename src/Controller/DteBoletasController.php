<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Controller\DteFoliosController;

\sasco\LibreDTE\Sii::setAmbiente(\sasco\LibreDTE\Sii::CERTIFICACION);
define("CERT_BOLETAS", ROOT . DS . 'files' . DS . 'certificacion' . DS);
define("FILE_BOLETAS", 'EnvioBOLETAS');
/**
 * DteBoletas Controller
 *
 * @property \App\Model\Table\DteBoletasTable $DteBoletas
 *
 * @method \App\Model\Entity\DteBoleta[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DteBoletasController extends AppController
{

    public function certificar()
    {
        
        if ($this->request->is('post')) {

            if (!empty($this->request->data["data"]) && !empty($this->request->data["file"])) {
                $this->request->data["data"] = json_decode($this->request->data["data"], true);
            }
            else {
                echo json_encode(["message" => "Debe completar todos los campos antes de enviar la solicitud", "data" => []]); 
                exit;
            }

            if (!empty($this->request->data["data"]["caratula"]) && !empty($this->request->data["data"]["emisor"]) && !empty($this->request->data["data"]["receptor"]) && !empty($this->request->data["data"]["dataPruebas"])){

                $caratula = $this->request->data["data"]["caratula"];
                $Emisor = $this->request->data["data"]["emisor"];
                $Receptor = $this->request->data["data"]["receptor"];
                $documentos = $this->request->data["data"]["dataPruebas"];
                $file = $this->request->data["file"];
                $pathXML = CERT_BOLETAS . $Emisor["RUTEmisor"] . DS . 'xml' . DS . FILE_BOLETAS . '.xml';
                $pathCAF = CERT_BOLETAS . $Emisor["RUTEmisor"] . DS . 'folios' . DS . basename($file['name']);
                move_uploaded_file($file['tmp_name'], $pathCAF);
                
                $foliosTipo = [ 39 => 1 ];

                $boleta["xml"] = $this->setBoleta($foliosTipo, $caratula, $Emisor, $Receptor, $documentos);
                
                $dom = new \DOMDocument;
                $dom->preserveWhiteSpace = TRUE;
                $dom->loadXML(trim($boleta["xml"]));
                $dom->save($pathXML);

                header('Content-type: text/xml');
                header('Content-Disposition: attachment; filename='.FILE_BOLETAS.'.xml');

                echo $dom->saveXML() . "\n";

            } else {
                echo json_encode(["message" => "Debe completar todos los campos antes de enviar la solicitud.", "data" => []]);
            }            
        }      
        exit;  
    }

    public function setBoleta($folios, $caratula, $Emisor, $Receptor, $documentos){
        // Objetos de Firma y Folios
        $config = AppController::config();
        $Folios = [];
        $pathXML = CERT_BOLETAS . $Emisor["RUTEmisor"] . DS . 'folios' . DS;
        $Firma = new \sasco\LibreDTE\FirmaElectronica($config['firma']);
        
        foreach ($folios as $tipo => $cantidad)
            $Folios[$tipo] = new \sasco\LibreDTE\Sii\Folios(file_get_contents($pathXML.$tipo.'.xml'));
        // generar cada DTE, timbrar, firmar y agregar al sobre de EnvioBOLETA
        $EnvioDTE = new \sasco\LibreDTE\Sii\EnvioDte();        
        foreach ($documentos as $documento) {
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

    public function certificaEnvio(){
        $config = AppController::config();
        if ($this->request->is('post')) {
            if (!empty($this->request->data["rutEmisor"]) && !empty($this->request->data["rutEnvia"])) {
                // datos del envío
                
                $RutEnvia = $this->request->data["rutEmisor"];
                $RutEmisor = $this->request->data["rutEnvia"];
                $pathXML = CERT_BOLETAS . $RutEmisor . DS . 'xml' . DS . FILE_BOLETAS . '.xml';
                $xml = file_get_contents($pathXML);
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
                echo json_encode(["message" => "OK", "data" => $result->TRACKID."" ]);  //'DTE envíado. Track ID '.$result->TRACKID,"\n";
            } else {
                echo json_encode(["message" => "Debe completar todos los campos antes de enviar la solicitud" , "data" => "" ]);
            }
            exit;
        } 
        
    }

    public function certificaPdf(){
        
        if ($this->request->is('post')) {
            if (!empty($this->request->data["rutEmisor"])) {
                $config = AppController::config();
                $RutEmisor = $this->request->data["rutEmisor"];
                $pathXML = CERT_BOLETAS . $RutEmisor . DS . 'xml' . DS . FILE_BOLETAS . '.xml';                        
                $EnvioDte = new \sasco\LibreDTE\Sii\EnvioDte();
                $EnvioDte->loadXML(file_get_contents($pathXML));
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
                    $footer = [
                        'left' => 'Valparaíso: Pjse Azalea oriente 2768 Villa Alemana. WhatsApp +56 9 6589 9508',
                        'right' => 'http://www.neonet.cl',
                    ];
                    $pdf->setFooterText($footer);
                    $pdf->setLogo(CERT_BOLETAS . $RutEmisor . DS . 'logo.png'); // debe ser PNG!
                    $pdf->setResolucion(['FchResol'=>$Caratula['FchResol'], 'NroResol'=>$Caratula['NroResol']]);
                    //$pdf->setCedible(true);
                    $pdf->agregar($DTE->getDatos(), $DTE->getTED());
                    $id = str_replace('LibreDTE_', '', $DTE->getID());                    
                    $pdf->Output($dir.'/dte_'.$Caratula['RutEmisor'].'_'.$id.'.pdf', 'F');
                }
                // entregar archivo comprimido que incluirá cada uno de los DTEs
                \sasco\LibreDTE\File::compress($dir, ['format'=>'zip', 'delete'=>true]);
            }
        }
        exit;        
    }


    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['DteDocumentos']
        ];
        $dteBoletas = $this->paginate($this->DteBoletas);

        $this->set(compact('dteBoletas'));
    }

    /**
     * View method
     *
     * @param string|null $id Dte Boleta id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $dteBoleta = $this->DteBoletas->get($id, [
            'contain' => ['DteDocumentos']
        ]);

        $this->set('dteBoleta', $dteBoleta);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $dteBoleta = $this->DteBoletas->newEntity();
        if ($this->request->is('post')) {
            $dteBoleta = $this->DteBoletas->patchEntity($dteBoleta, $this->request->getData());
            if ($this->DteBoletas->save($dteBoleta)) {
                $this->Flash->success(__('The dte boleta has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dte boleta could not be saved. Please, try again.'));
        }
        $dteDocumentos = $this->DteBoletas->DteDocumentos->find('list', ['limit' => 200]);
        $this->set(compact('dteBoleta', 'dteDocumentos'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Dte Boleta id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $dteBoleta = $this->DteBoletas->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $dteBoleta = $this->DteBoletas->patchEntity($dteBoleta, $this->request->getData());
            if ($this->DteBoletas->save($dteBoleta)) {
                $this->Flash->success(__('The dte boleta has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The dte boleta could not be saved. Please, try again.'));
        }
        $dteDocumentos = $this->DteBoletas->DteDocumentos->find('list', ['limit' => 200]);
        $this->set(compact('dteBoleta', 'dteDocumentos'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Dte Boleta id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $dteBoleta = $this->DteBoletas->get($id);
        if ($this->DteBoletas->delete($dteBoleta)) {
            $this->Flash->success(__('The dte boleta has been deleted.'));
        } else {
            $this->Flash->error(__('The dte boleta could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
