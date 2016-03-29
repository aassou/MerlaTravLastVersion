<?php
    //classes loading begin
    function classLoad ($myClass) {
        if(file_exists('../model/'.$myClass.'.php')){
            include('../model/'.$myClass.'.php');
        }
        elseif(file_exists('../controller/'.$myClass.'.php')){
            include('../controller/'.$myClass.'.php');
        }
    }
    spl_autoload_register("classLoad"); 
    include('../config.php');  
    //classes loading end
    session_start();
    if( isset($_SESSION['userMerlaTrav']) ){
        //classes managers  
        $projetManager = new ProjetManager($pdo);
        $caisseManager = "";
        $projets = $projetManager->getProjets();
        $titre = "";
        $caisses = "";
        $titreDocument = "";
        $totalCaisse = 0;
        $societe = htmlentities($_POST['societe']);
        if ( $societe == 1 ) {
            $caisseManager = new CaisseManager($pdo);
            $titre = "Société MerlaTrav";
        }
        else if ( $societe == 2 ) {
            $caisseManager = new CaisseIaazaManager($pdo);
            $titre = "Société Iaaza";
        }
        
        $criteria = htmlentities($_POST['criteria']);
        if( $criteria=="parDate" ) {
            $dateFrom = htmlentities($_POST['dateFrom']);
            $dateTo = htmlentities($_POST['dateTo']); 
            $type = htmlentities($_POST['type']);
            $destination = htmlentities($_POST['destination']);
            if( $type == "Toutes" ) {
                $caisses = $caisseManager->getCaissesByDatesByDestination($dateFrom, $dateTo, $destination);
                $titreDocument = "Liste des opérations pour ".$destination." entre : ".date('d/m/Y', strtotime($dateFrom)).' - '.date('d/m/Y', strtotime($dateTo));
                $totalCaisse = 
                $caisseManager->getTotalCaisseByTypeByDateByDestination('Entree', $dateFrom, $dateTo, $destination) - $caisseManager->getTotalCaisseByTypeByDateByDestination('Sortie', $dateFrom, $dateTo, $destination);   
            }
            else {
                $caisses = $caisseManager->getCaissesByDatesByTypeByDestination($dateFrom, $dateTo, $type, $destination);
                $titreDocument = "Liste des opérations pour ".$destination." d'".$type." entre : ".date('d/m/Y', strtotime($dateFrom)).' - '.date('d/m/Y', strtotime($dateTo));
                $totalCaisse = 
                $caisseManager->getTotalCaisseByTypeByDateByDestination($type, $dateFrom, $dateTo, $destination);
            }
        }
        else if ( $criteria=="toutesCaisse" ) {
            $caisses = $caisseManager->getCaisses();
            $titreDocument = "Bilan de toutes les opérations de caisse";
            $totalCaisse = $caisseManager->getTotalCaisseByType('Entree') - $caisseManager->getTotalCaisseByType('Sortie');   
            /*if ( isset($_POST['type']) ) {
                $type = htmlentities($_POST['type']);
                $caisses = $caisseManager->getCaissesByType($type);
                $titreDocument = "Liste des opérations de type : ".$type;
            }*/
        }

ob_start();
?>
<style type="text/css">
    p, h1{
        text-align: center;
        text-decoration: underline;
    }
    table {
            border-collapse: collapse;
            width:100%;
        }
        
        table, th, td {
            border: 1px solid black;
        }
        td, th{
            padding : 5px;
        }
        
        th{
            background-color: grey;
        }
</style>
<page backtop="15mm" backbottom="20mm" backleft="10mm" backright="10mm">
    <!--img src="../assets/img/logo_company.png" style="width: 110px" /-->
    <h1><?= $titre ?></h1>
    <h1><?= $titreDocument ?></h1>
    <p>Imprimé le <?= date('d-m-Y') ?> | <?= date('h:i') ?> </p>
    <br>
    <table>
        <tr>
            <!--th style="width: 20%">Type</th-->
            <th style="width: 15%">Date opération</th>
            <th style="width: 15%">Crédit</th>
            <th style="width: 15%">Débit</th>
            <th style="width: 30%">Désignation</th>
            <th style="width: 25%">Destination</th>
        </tr>
        <?php
        foreach($caisses as $caisse){
        ?>      
        <tr>
            <td style="width: 15%"><?= date('d/m/Y', strtotime($caisse->dateOperation())) ?></td>
            <?php
            if ( $caisse->type() == "Entree" ) {
            ?>
            <td style="width: 15%"><?= number_format($caisse->montant(), 2, ' ', ',') ?></td>
            <td style="width: 15%"></td>
            <?php  
            }
            else {
            ?>
            <td style="width: 15%"></td>
            <td style="width: 15%"><?= number_format($caisse->montant(), 2, ' ', ',') ?></td>
            <?php
            }
            ?>
            <td style="width: 30%"><?= $caisse->designation() ?></td>
            <td style="width: 25%"><?= $caisse->destination() ?></td>
        </tr>   
        <?php
        }//end of loop
        ?>
    </table>
    <table>
        <tr>
            <th style="width: 20%">Solde</th>
            <td style="width: 40%"><strong><?= number_format($totalCaisse, 2, ' ', ',') ?>&nbsp;DH</strong></td>
            <td style="width: 20%"></td>
            <td style="width: 20%"></td>
        </tr>
    </table>
    <br><br>
    <page_footer>
    <hr/>
    <p style="text-align: center;font-size: 9pt;"></p>
    </page_footer>
</page>    
<?php
    $content = ob_get_clean();
    
    require('../lib/html2pdf/html2pdf.class.php');
    try{
        $pdf = new HTML2PDF('P', 'A4', 'fr');
        $pdf->pdf->SetDisplayMode('fullpage');
        $pdf->writeHTML($content);
        $fileName = "BilanCaisse-".date('Ymdhi').'.pdf';
        $pdf->Output($fileName);
    }
    catch(HTML2PDF_exception $e){
        die($e->getMessage());
    }
}
else{
    header("Location:index.php");
}
?>