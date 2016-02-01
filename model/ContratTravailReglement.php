<?php
class ContratTravailReglement{

	//attributes
	private $_id;
	private $_montant;
	private $_motif;
	private $_dateReglement;
	private $_idContratTravail;
    private $_created;
    private $_createdBy;
    private $_updated;
    private $_updatedBy;

	//le constructeur
    public function __construct($data){
        $this->hydrate($data);
    }
    
    //la focntion hydrate sert à attribuer les valeurs en utilisant les setters d\'une façon dynamique!
    public function hydrate($data){
        foreach ($data as $key => $value){
            $method = 'set'.ucfirst($key);
            
            if (method_exists($this, $method)){
                $this->$method($value);
            }
        }
    }

	//setters
	public function setId($id){
    	$this->_id = $id;
    }
	public function setMontant($montant){
		$this->_montant = $montant;
   	}

	public function setMotif($motif){
		$this->_motif = $motif;
   	}

	public function setDateReglement($dateReglement){
		$this->_dateReglement = $dateReglement;
   	}

	public function setIdContratTravail($idContratTravail){
		$this->_idContratTravail = $idContratTravail;
   	}
    
    public function setCreated($created){
        $this->_created = $created;
    }
    
    public function setCreatedBy($createdBy){
        $this->_createdBy = $createdBy;
    }
    
    public function setUpdated($updated){
        $this->_updated = $updated;
    }
    
    public function setUpdatedBy($updatedBy){
        $this->_updatedBy = $updatedBy;
    }

	//getters
	public function id(){
    	return $this->_id;
    }
	public function montant(){
		return $this->_montant;
   	}

	public function motif(){
		return $this->_motif;
   	}

	public function dateReglement(){
		return $this->_dateReglement;
   	}

	public function idContratTravail(){
		return $this->_idContratTravail;
   	}
    
    public function created(){
        return $this->_created;
    }
    
    public function createdBy(){
        return $this->_createdBy;
    }
    
    public function updated(){
        return $this->_updated;
    }
    
    public function updatedBy(){
        return $this->_updatedBy;
    }

}