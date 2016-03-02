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
        $projetManager = new ProjetManager($pdo);
        $clientManager = new ClientManager($pdo);
        $contratManager = new ContratManager($pdo);
        $operationManager = new OperationManager($pdo);
        $compteBancaireManager = new CompteBancaireManager($pdo);
        $contratCasLibreManager = new ContratCasLibreManager($pdo);
        $reglementPrevuManager = new ReglementPrevuManager($pdo);
        //reglements prevus
        $reglementsPrevusEnRetards = $reglementPrevuManager->getReglementPrevuEnRetard();
        $reglementsPrevusToday = $reglementPrevuManager->getReglementPrevuToday();
        $reglementsPrevusWeek = $reglementPrevuManager->getReglementPrevuWeek();
        $reglementsPrevusMonth = $reglementPrevuManager->getReglementPrevuMonth();
        //casLibre dates
        $casLibreEnRetards = $contratCasLibreManager->getReglementEnRetard();
        $casLibreToday = $contratCasLibreManager->getReglementToday();
        $casLibreWeek = $contratCasLibreManager->getReglementWeek();
        $casLibreMonth = $contratCasLibreManager->getReglementMonth();

ob_start();
?>
<style type="text/css">
    p, h1, h2, h3{
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
<page backtop="5mm" backbottom="20mm" backleft="10mm" backright="10mm">
    <img src="../assets/img/logo-new.jpg" />
    <h2>Liste des clients en retards</h2>
    <table>
        <tr>
            <th style="width: 20%">Client</th>
            <th style="width: 15%">Téléphone</th>
            <th style="width: 15%">Projet</th>
            <th style="width: 20%">Bien</th>
            <th style="width: 15%">Montant</th>
            <th style="width: 15%">Date Prévu</th>
        </tr>
        <?php
        foreach ( $reglementsPrevusEnRetards as $element ) {
            $contrat = 
            $contratManager->getContratByCode($element->codeContrat());
            $client = 
            $clientManager->getClientById($contrat->idClient());
            $projet = 
            $projetManager->getProjetById($contrat->idProjet());
            $bien = "";
            $typeBien = "";
            //if the property is a "Local commercial" we don't need to mention niveau attribute
            $niveau = "";
            if($contrat->typeBien()=="appartement"){
                $appartementManager = new AppartementManager($pdo);
                $bien = $appartementManager->getAppartementById($contrat->idBien());
                $niveau = $bien->niveau();
                $typeBien = "Appartement";
            }
            else if($contrat->typeBien()=="localCommercial"){
                $locauxManager = new LocauxManager($pdo);
                $bien = $locauxManager->getLocauxById($contrat->idBien());
                $typeBien = "Local Commercial";
            }
        ?>
        <tr>
            <td style="width: 20%"><?= $client->nom() ?></td>
            <td style="width: 15%"><?= $client->telephone1() ?></td>
            <td style="width: 15%"><?= $projet->nom() ?></td>
            <td style="width: 20%"><?= $typeBien.' - '.$niveau.'e: '.$bien->nom() ?></td>
            <td style="width: 15%"><?= number_format($contrat->echeance(), 2, ',', ' ') ?>DH</td>
            <td style="width: 15%"><?= date('d/m/Y', strtotime($element->datePrevu())) ?></td>
        </tr>
        <?php
        }
        ?>
        <?php
        foreach ( $casLibreEnRetards as $element ) {
            $contrat = 
            $contratManager->getContratByCode($element->codeContrat());
            $client = 
            $clientManager->getClientById($contrat->idClient());
            $projet = 
            $projetManager->getProjetById($contrat->idProjet());
            $bien = "";
            $typeBien = "";
            //if the property is a "Local commercial" we don't need to mention niveau attribute
            $niveau = "";
            if($contrat->typeBien()=="appartement"){
                $appartementManager = new AppartementManager($pdo);
                $bien = $appartementManager->getAppartementById($contrat->idBien());
                $niveau = $bien->niveau();
                $typeBien = "Appartement";
            }
            else if($contrat->typeBien()=="localCommercial"){
                $locauxManager = new LocauxManager($pdo);
                $bien = $locauxManager->getLocauxById($contrat->idBien());
                $typeBien = "Local Commercial";
            }
        ?>
        <tr>
            <td style="width: 20%"><?= $client->nom() ?></td>
            <td style="width: 15%"><?= $client->telephone1() ?></td>
            <td style="width: 15%"><?= $projet->nom() ?></td>
            <td style="width: 20%"><?= $typeBien.' - '.$niveau.'e: '.$bien->nom() ?></td>
            <td style="width: 15%"><?= number_format($contrat->echeance(), 2, ',', ' ') ?>DH</td>
            <td style="width: 15%"><?= date('d/m/Y', strtotime($element->date())) ?></td>
        </tr>
        <?php
        }
        ?>    
    </table>
    <page_footer>
    <hr/>
    <p style="text-align: center">STE MERLA TRAV SARL : Au capital de 100 000,00 DH – Siège social Hay Al Matar En face de l'institution AR'RISSALA 2,
    Nador.
    Tèl 0536381458/ 0661668860 IF : 40451179 RC : 10999 Patente 56126681</p>
    </page_footer>
</page>    
<?php
    $content = ob_get_clean();
    
    require('../lib/html2pdf/html2pdf.class.php');
    try{
        $pdf = new HTML2PDF('P', 'A4', 'fr');
        $pdf->pdf->SetDisplayMode('fullpage');
        $pdf->writeHTML($content);
        $fileName = "Etats-Contrats-Clients-".date('Y-m-d-h-i').'.pdf';
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