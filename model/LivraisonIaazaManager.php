<?php
class LivraisonIaazaManager{
    //attributes
    private $_db;
    
    //constructor
    public function __construct($db){
        $this->_db = $db;
    }
    
    //CRUD operations
    public function add(LivraisonIaaza $livraison){
        $query = $this->_db->prepare(
        'INSERT INTO t_livraison_iaaza (dateLivraison, libelle, designation, idFournisseur, idProjet, code)
        VALUES (:dateLivraison, :libelle, :designation, :idFournisseur, :idProjet, :code)') 
        or die(print_r($this->_db->errorInfo()));
        $query->bindValue(':dateLivraison', $livraison->dateLivraison());
		$query->bindValue(':libelle', $livraison->libelle());
        $query->bindValue(':designation', $livraison->designation());
        $query->bindValue(':idFournisseur', $livraison->idFournisseur());
        $query->bindValue(':idProjet', $livraison->idProjet());
		$query->bindValue(':code', $livraison->code());
        $query->execute();
        $query->closeCursor();
    }

    public function update(LivraisonIaaza $livraison){
        $query = $this->_db->prepare(
        'UPDATE t_livraison_iaaza SET dateLivraison=:dateLivraison, libelle=:libelle, designation=:designation,
        idProjet=:idProjet, idFournisseur=:idFournisseur, updated=:updated, updatedBy=:updatedBy
        WHERE id=:id') or die(print_r($this->_db->errorInfo()));
        $query->bindValue(':id', $livraison->id());
        $query->bindValue(':dateLivraison', $livraison->dateLivraison());
		$query->bindValue(':libelle', $livraison->libelle());
        $query->bindValue(':designation', $livraison->designation());
        $query->bindValue(':idProjet', $livraison->idProjet());
        $query->bindValue(':idFournisseur', $livraison->idFournisseur());
        $query->bindValue(':updated', $livraison->updated());
        $query->bindValue(':updatedBy', $livraison->updatedBy());
        $query->execute();
        $query->closeCursor();
    }
	
	public function delete($idLivraison){
		$query = $this->_db->prepare('DELETE FROM t_livraison_iaaza WHERE id=:idLivraison')
		or die(print_r($this->_db->errorInfo()));;
		$query->bindValue(':idLivraison', $idLivraison);
		$query->execute();
		$query->closeCursor();
	}
    
	public function getLivraisonsByLimit($begin, $end){
		$livraisons = array();
        $query = $this->_db->query('SELECT * FROM t_livraison_iaaza GROUP BY idFournisseur ORDER BY id DESC LIMIT '.$begin.', '.$end);
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
	}
    
    public function getLivraisonsByGroup(){
        $livraisons = array();
        $query = $this->_db->query('SELECT * FROM t_livraison_iaaza GROUP BY idFournisseur ORDER BY id DESC');
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
    }
    
    public function getLivraisonsByGroupByType($type){
        $livraisons = array();
        $query = $this->_db->prepare(
        'SELECT * FROM t_livraison_iaaza WHERE type=:type GROUP BY idFournisseur ORDER BY id DESC');
        $query->bindValue(':type', $type);
        $query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
    }
	
    /*public function getLivraisonsByIdFournisseur($idFournisseur){
        $livraisons = array();
        $query = $this->_db->prepare('SELECT * FROM t_livraison_iaaza WHERE idFournisseur=:idFournisseur ORDER BY dateLivraison DESC');
        $query->bindValue(':idFournisseur', $idFournisseur);
        $query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
    }*/
	
	public function getLivraisonsByIdFournisseurByLimits($idFournisseur, $begin, $end){
        $livraisons = array();
        $query = $this->_db->prepare('SELECT * FROM t_livraison_iaaza WHERE idFournisseur=:idFournisseur
        ORDER BY id DESC LIMIT '.$begin.', '.$end);
        $query->bindValue(':idFournisseur', $idFournisseur);
        $query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
    }
    
    public function getLivraisonToday(){
        $livraisons = array();
        $query = $this->_db->query('SELECT * FROM t_livraison_iaaza WHERE dateLivraison=CURDATE() ORDER BY dateLivraison DESC');
        $query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
    }
    
    public function getLivraisonYesterday(){
        $livraisons = array();
        $query = $this->_db->query('SELECT * FROM t_livraison_iaaza WHERE dateLivraison=SUBDATE(CURDATE(),1) ORDER BY dateLivraison DESC');
        $query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
    }
    
    public function getLivraisonsWeek(){
        $livraisons = array();
        $query = $this->_db->query('SELECT * FROM t_livraison_iaaza WHERE dateLivraison BETWEEN SUBDATE(CURDATE(),7) AND CURDATE() ORDER BY dateLivraison DESC');
        $query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
    }
	
	public function getLivraisonsNumberWeek(){
        $query = $this->_db->query('SELECT COUNT(*) AS numberLivraison FROM t_livraison_iaaza WHERE dateLivraison BETWEEN SUBDATE(CURDATE(),7) AND CURDATE()');
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();
        return $data['numberLivraison'];
    }
    
    public function getLivraisonsByIdProjet($idProjet){
        $livraisons = array();
        $query = $this->_db->prepare('SELECT * FROM t_livraison_iaaza WHERE idProjet=:idProjet ORDER BY dateLivraison');
        $query->bindValue(':idProjet', $idProjet);
        $query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
    }
	
	public function getLivraisonsByIdProjetByLimit($idProjet, $begin, $end){
        $livraisons = array();
        $query = $this->_db->prepare('SELECT * FROM t_livraison_iaaza WHERE idProjet=:idProjet ORDER BY dateLivraison DESC LIMIT '.$begin.', '.$end);
        $query->bindValue(':idProjet', $idProjet);
        $query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
    }
	
	public function getLivraisonsByIdFournisseurByLimit($idFournisseur, $begin, $end){
        $livraisons = array();
        $query = $this->_db->prepare('SELECT * FROM t_livraison_iaaza WHERE idFournisseur=:idFournisseur ORDER BY dateLivraison DESC LIMIT '.$begin.', '.$end);
        $query->bindValue(':idFournisseur', $idFournisseur);
        $query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
    }
	
	public function getLivraisonsNumberByIdProjet($idProjet){
		$query = $this->_db->query('SELECT COUNT(*) AS livraisonNumbers FROM t_livraison_iaaza WHERE idProjet='.$idProjet);
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();
        return $data['livraisonNumbers'];
	}
	
	public function getLivraisonsNumberByIdFournisseur($idFournisseur){
		$query = $this->_db->query('SELECT COUNT(*) AS livraisonNumbers FROM t_livraison_iaaza WHERE idFournisseur='.$idFournisseur);
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();
        return $data['livraisonNumbers'];
	}
    
    public function getLivraisonNumber(){
        $query = $this->_db->query('SELECT COUNT(*) AS livraisonNumber FROM t_livraison_iaaza');
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();
        return $data['livraisonNumber'];
    }
    
	public function getFournisseursByIdProjet($idProjet){
		$fournisseurs = array();
		$query = $this->_db->prepare(" SELECT DISTINCT f.id AS id, nom FROM t_fournisseur f INNER JOIN t_livraison_iaaza l 
		ON l.idFournisseur = f.id WHERE l.idProjet=:idProjet ")
		or die(print_r($this->_db->errorInfo()));
		$query->bindValue(':idProjet', $idProjet);
		$query->execute();
		while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $fournisseurs[] = new Fournisseur($data);
        }
        $query->closeCursor();
		return $fournisseurs;
	}
	
    public function getIdFournisseurByIdProject($idProjet){
        $idsFournisseur = array();
        $query = $this->_db->prepare('SELECT DISTINCT idFournisseur FROM t_livraison_iaaza WHERE idProjet=:idProjet');
        $query->bindValue(':idProjet', $idProjet);
        $query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = $data['idFournisseur'];
        }
        $query->closeCursor();
        if(!empty($livraisons)){
            return $livraisons;    
        }
        else{
            return 1;
        }
    }
    
    public function getLivraisonById($id){
        $query = $this->_db->prepare('SELECT * FROM t_livraison_iaaza WHERE id=:id ORDER BY dateLivraison DESC');
        $query->bindValue(':id', $id);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();
        return new LivraisonIaaza($data);
    }
    
    
    public function resteTotal(){
        $query = $this->_db->query('SELECT SUM(reste) AS credit FROM t_livraison_iaaza');
        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data['credit'];
    }
    
    public function getLastId(){
        $query = $this->_db->query('SELECT id AS last_id FROM t_livraison_iaaza ORDER BY id DESC LIMIT 0, 1');
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $id = $data['last_id'];
        return $id;
    }

	public function getLivraisonByCode($code){
        $query = $this->_db->prepare('SELECT * FROM t_livraison_iaaza WHERE code=:code ORDER BY created DESC');
        $query->bindValue(':code', $code);
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();
        return new LivraisonIaaza($data);
    }
	
	public function getCodeLivraison($code){
        $query = $this->_db->prepare('SELECT code FROM t_livraison_iaaza WHERE code=:code');
		$query->bindValue(':code', $code);
		$query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data['code'];
    }

	public function getSommeLivraisonsByIdProjetAndIdFournisseur($idProjet, $idFournisseur){
		$query = $this->_db->prepare(' SELECT SUM(prixUnitaire*quantite) AS total FROM t_livraison_iaaza 
		WHERE idProjet=:idProjet AND idFournisseur=:idFournisseur ');
		$query->bindValue(':idProjet', $idProjet);
		$query->bindValue(':idFournisseur', $idFournisseur);
		$query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data['total'];
	}
	
	public function getTotalLivraisonsIdFournisseur($idFournisseur){
		$query = $this->_db->prepare(' SELECT SUM(prixUnitaire*quantite) AS total FROM t_livraison_iaaza 
		WHERE idFournisseur=:idFournisseur ');
		$query->bindValue(':idFournisseur', $idFournisseur);
		$query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data['total'];
	}

	public function getTotalLivraisonsIdProjet($idProjet){
		$query = $this->_db->prepare(' SELECT SUM(prixUnitaire*quantite) AS total FROM t_livraison_iaaza 
		WHERE idProjet=:idProjet ');
		$query->bindValue(':idProjet', $idProjet);
		$query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data['total'];
	}

	public function getTotalLivraisons(){
		$query = $this->_db->query('SELECT SUM(prixUnitaire*quantite) AS total FROM t_livraison_iaaza');
        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data['total'];
	}
	
	//new functions
	
	public function getLivraisonsNumberByIdFournisseurByProjet($idFournisseur, $idProjet){
		$query = $this->_db->query('SELECT COUNT(*) AS livraisonNumbers FROM t_livraison_iaaza WHERE idFournisseur='.$idFournisseur.' AND idProjet='.$idProjet);
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();
        return $data['livraisonNumbers'];
	}
	
	public function getLivraisonsByIdFournisseurByProjetByLimits($idFournisseur, $idProjet, $begin, $end){
        $livraisons = array();
        $query = $this->_db->prepare('SELECT * FROM t_livraison_iaaza WHERE idFournisseur=:idFournisseur
        AND idProjet=:idProjet
        ORDER BY dateLivraison DESC LIMIT '.$begin.', '.$end);
        $query->bindValue(':idFournisseur', $idFournisseur);
		$query->bindValue(':idProjet', $idProjet);
        $query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
    }
	
	public function getLivraisonIdsByIdFournisseur($idFournisseur){
		$ids = array();
		$query = $this->_db->prepare(' SELECT id FROM t_livraison_iaaza 
		WHERE idFournisseur=:idFournisseur');
		$query->bindValue(':idFournisseur', $idFournisseur);
		$query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $ids[] = $data['id'];
        }
        return $ids;
	}
	
	public function getLivraisonIdsByIdFournisseurIdProjet($idFournisseur, $idProjet){
		$ids = array();
		$query = $this->_db->prepare(' SELECT id FROM t_livraison_iaaza 
		WHERE idFournisseur=:idFournisseur AND idProjet=:idProjet ');
		$query->bindValue(':idFournisseur', $idFournisseur);
		$query->bindValue(':idProjet', $idProjet);
		$query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $ids[] = $data['id'];
        }
        return $ids;
	}
	
	public function getTotalLivraisonsIdFournisseurProjet($idFournisseur, $idProjet){
		$query = $this->_db->prepare(' SELECT SUM(prixUnitaire*quantite) AS total FROM t_livraison_iaaza 
		WHERE idFournisseur=:idFournisseur AND idProjet=:idProjet ');
		$query->bindValue(':idFournisseur', $idFournisseur);
		$query->bindValue(':idProjet', $idProjet);
		$query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        return $data['total'];
	}
	
	public function getLivraisonsByIdFournisseurByProjet($idFournisseur, $idProjet){
        $livraisons = array();
        $query = $this->_db->prepare('SELECT * FROM t_livraison_iaaza WHERE idFournisseur=:idFournisseur
        AND idProjet=:idProjet ORDER BY dateLivraison DESC');
        $query->bindValue(':idFournisseur', $idFournisseur);
		$query->bindValue(':idProjet', $idProjet);
        $query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
    }
	
	public function getLivraisonsByIdFournisseur($idFournisseur){
        $livraisons = array();
        $query = $this->_db->prepare('SELECT * FROM t_livraison_iaaza WHERE idFournisseur=:idFournisseur ORDER BY dateLivraison DESC');
        $query->bindValue(':idFournisseur', $idFournisseur);
        $query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
    }
	
	public function getLivraisons(){
		$livraisons = array();
        $query = $this->_db->query('SELECT * FROM t_livraison_iaaza ORDER BY dateLivraison DESC');
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
	}
    
    /************************************************************************************************
     *                                                                                              * 
     *                     These new methods are created for AnnahdaProjet                          *
     *                                                                                              *
     ************************************************************************************************/
    
    public function getLivraisonsByIdFournisseurByDates($idFournisseur, $dateFrom, $dateTo){
        $livraisons = array();
        $query = $this->_db->prepare('SELECT * FROM t_livraison_iaaza WHERE 
        idFournisseur=:idFournisseur AND dateLivraison BETWEEN :dateFrom AND :dateTo 
        ORDER BY dateLivraison DESC');
        $query->bindValue(':idFournisseur', $idFournisseur);
        $query->bindValue(':dateFrom', $dateFrom);
        $query->bindValue(':dateTo', $dateTo);
        $query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            $livraisons[] = new LivraisonIaaza($data);
        }
        $query->closeCursor();
        return $livraisons;
    }
}