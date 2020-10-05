<?php

class Model_DbTable_DossierAffectation extends Zend_Db_Table_Abstract
{
    protected $_name="dossieraffectation"; // Nom de la base
    protected $_primary = array("ID_DATECOMMISSION_AFFECT","ID_DOSSIER_AFFECT"); // Clé primaire

	public function getDossierNonAffect($idDateCom)
    {
        //retourne l'ensemble des dossiers programés à la date de comm passée en param et dont les horaires N'ONT PAS été précisés
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('doss' => 'dossier'))
			->join(array('dossAffect' => 'dossieraffectation'),'doss.ID_DOSSIER = dossAffect.ID_DOSSIER_AFFECT')
			->join(array('dateComm' => 'datecommission'),'dossAffect.ID_DATECOMMISSION_AFFECT = dateComm.ID_DATECOMMISSION')
			->join(array('dossNat' => 'dossiernature'),'dossNat.ID_DOSSIER = doss.ID_DOSSIER')
			->join(array('dossNatListe' => 'dossiernatureliste'),'dossNat.ID_NATURE = dossNatListe.ID_DOSSIERNATURE')
            ->join(array('dossType' => "dossiertype"), 'doss.TYPE_DOSSIER = dossType.ID_DOSSIERTYPE', 'LIBELLE_DOSSIERTYPE')
            ->joinLeft(array("e" => "etablissementdossier"), "doss.ID_DOSSIER = e.ID_DOSSIER", null)
            ->joinLeft("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )", "LIBELLE_ETABLISSEMENTINFORMATIONS")
            ->joinLeft("etablissementadresse", "etablissementinformations.ID_ETABLISSEMENT = etablissementadresse.ID_ETABLISSEMENT", null)
            ->joinLeft("adressecommune", "etablissementadresse.NUMINSEE_COMMUNE = adressecommune.NUMINSEE_COMMUNE", "LIBELLE_COMMUNE")
            ->joinLeft(array("dp1" => "dossierpreventionniste"),"dp1.ID_DOSSIER = doss.ID_DOSSIER",null)
            ->joinLeft(array("u1" => "utilisateur"),"u1.ID_UTILISATEUR = dp1.ID_PREVENTIONNISTE",null)
            ->joinLeft(array("ui1" => "utilisateurinformations"),"ui1.ID_UTILISATEURINFORMATIONS = u1.ID_UTILISATEURINFORMATIONS",array("PRENOM_UTILISATEURINFORMATIONS1" => "PRENOM_UTILISATEURINFORMATIONS", "NOM_UTILISATEURINFORMATIONS1" => "NOM_UTILISATEURINFORMATIONS"))
            ->joinLeft(array("dp2" => "dossierpreventionniste"),"dp2.ID_DOSSIER = doss.ID_DOSSIER and dp2.ID_PREVENTIONNISTE <> dp1.ID_PREVENTIONNISTE",null)
            ->joinLeft(array("u2" => "utilisateur"),"u2.ID_UTILISATEUR = dp2.ID_PREVENTIONNISTE",null)
            ->joinLeft(array("ui2" => "utilisateurinformations"),"ui2.ID_UTILISATEURINFORMATIONS = u2.ID_UTILISATEURINFORMATIONS",array("PRENOM_UTILISATEURINFORMATIONS2" => "PRENOM_UTILISATEURINFORMATIONS", "NOM_UTILISATEURINFORMATIONS2" => "NOM_UTILISATEURINFORMATIONS"))
			->where('dateComm.ID_DATECOMMISSION = ?',$idDateCom)
			->where("dossAffect.HEURE_DEB_AFFECT IS NULL")
			->where("dossAffect.HEURE_FIN_AFFECT IS NULL")
// 			->order("dossAffect.NUM_DOSSIER")
		    ->order("adressecommune.LIBELLE_COMMUNE")
		    ->order("etablissementinformations.LIBELLE_ETABLISSEMENTINFORMATIONS")
			->group("doss.ID_DOSSIER");

        return $this->getAdapter()->fetchAll($select);
    }

	public function getDossierAffect($idDateCom)
    {
        //retourne l'ensemble des dossiers programés à la date de comm passée en param et dont les horaires ONT été précisés

		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('doss' => 'dossier'))
			->join(array('dossAffect' => 'dossieraffectation'),'doss.ID_DOSSIER = dossAffect.ID_DOSSIER_AFFECT')
			->join(array('dateComm' => 'datecommission'),'dossAffect.ID_DATECOMMISSION_AFFECT = dateComm.ID_DATECOMMISSION')
			->join(array('dossNat' => 'dossiernature'),'dossNat.ID_DOSSIER = doss.ID_DOSSIER')
			->join(array('dossNatListe' => 'dossiernatureliste'),'dossNat.ID_NATURE = dossNatListe.ID_DOSSIERNATURE')
            ->join(array('dossType' => "dossiertype"), 'doss.TYPE_DOSSIER = dossType.ID_DOSSIERTYPE', 'LIBELLE_DOSSIERTYPE')
            ->joinLeft(array("e" => "etablissementdossier"), "doss.ID_DOSSIER = e.ID_DOSSIER", null)
            ->joinLeft("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )", "LIBELLE_ETABLISSEMENTINFORMATIONS")
            ->joinLeft(array("dp1" => "dossierpreventionniste"),"dp1.ID_DOSSIER = doss.ID_DOSSIER",null)
            ->joinLeft(array("u1" => "utilisateur"),"u1.ID_UTILISATEUR = dp1.ID_PREVENTIONNISTE",null)
            ->joinLeft(array("ui1" => "utilisateurinformations"),"ui1.ID_UTILISATEURINFORMATIONS = u1.ID_UTILISATEURINFORMATIONS",array("PRENOM_UTILISATEURINFORMATIONS1" => "PRENOM_UTILISATEURINFORMATIONS", "NOM_UTILISATEURINFORMATIONS1" => "NOM_UTILISATEURINFORMATIONS"))
            ->joinLeft(array("dp2" => "dossierpreventionniste"),"dp2.ID_DOSSIER = doss.ID_DOSSIER and dp2.ID_PREVENTIONNISTE <> dp1.ID_PREVENTIONNISTE",null)
            ->joinLeft(array("u2" => "utilisateur"),"u2.ID_UTILISATEUR = dp2.ID_PREVENTIONNISTE",null)
            ->joinLeft(array("ui2" => "utilisateurinformations"),"ui2.ID_UTILISATEURINFORMATIONS = u2.ID_UTILISATEURINFORMATIONS",array("PRENOM_UTILISATEURINFORMATIONS2" => "PRENOM_UTILISATEURINFORMATIONS", "NOM_UTILISATEURINFORMATIONS2" => "NOM_UTILISATEURINFORMATIONS"))
			->where("dateComm.ID_DATECOMMISSION = ?",$idDateCom)
			->where("dossAffect.HEURE_DEB_AFFECT IS NOT NULL")
			->where("dossAffect.HEURE_FIN_AFFECT IS NOT NULL")
			->group("doss.ID_DOSSIER");
        return $this->getAdapter()->fetchAll($select);
    }

    public function getAllDossierAffect($idDateCom)
    {
        $select = "SELECT ".$this->_name.".*
            FROM ".$this->_name.", dossier
            WHERE dossier.ID_DOSSIER = ".$this->_name.".ID_DOSSIER_AFFECT
            AND ".$this->_name.".ID_DATECOMMISSION_AFFECT = '".$idDateCom."';
        ";

        return $this->getAdapter()->fetchAll($select);
    }

    public function getListDossierAffect($idDateCom)
    {
        $select = "SELECT ID_DOSSIER,OBJET_DOSSIER, VERROU_DOSSIER
            FROM dossieraffectation , dossier
            WHERE dossier.ID_DOSSIER = dossieraffectation.ID_DOSSIER_AFFECT
            AND dossieraffectation.ID_DATECOMMISSION_AFFECT = '".$idDateCom."'";

        return $this->getAdapter()->fetchAll($select);
    }

    public function recupDateDossierAffect($idDossier)
    {
        $select = "SELECT *
            FROM ".$this->_name.", datecommission
            WHERE  datecommission.ID_DATECOMMISSION = ".$this->_name.".ID_DATECOMMISSION_AFFECT
            AND ".$this->_name.".ID_DOSSIER_AFFECT = '".$idDossier."'
            ORDER BY DATE_COMMISSION
        ";
        //echo $select;
        return $this->getAdapter()->fetchAll($select);
    }

    public function deleteDateDossierAffect($idDossier)
    {
        $delete = "DELETE
            FROM ".$this->_name."
            WHERE  ID_DOSSIER_AFFECT = '".$idDossier."'
        ";

        return $this->delete("ID_DOSSIER_AFFECT = '".$idDossier."'");
    }

	public function deleteDateDossierModifDateAffect($idDossier,$idDateComm)
    {
        $this->delete(array(
			'ID_DOSSIER_AFFECT = ?' => $idDossier,
			'ID_DATECOMMISSION_AFFECT <> ?' => $idDateComm
		));
    }

	public function getDossierAffectAndType($idDossier)
    {
        //récupèration des affectations du dossier ainsi que le type d'affectation (salle / visite / visite de comm)
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('doss' => 'dossier'))
			->join(array('dossAffect' => 'dossieraffectation'),'doss.ID_DOSSIER = dossAffect.ID_DOSSIER_AFFECT')
			->join(array('dateComm' => 'datecommission'),'dossAffect.ID_DATECOMMISSION_AFFECT = dateComm.ID_DATECOMMISSION')
			->where("doss.ID_DOSSIER = ?",$idDossier);
        return $this->getAdapter()->fetchAll($select);
    }

}
