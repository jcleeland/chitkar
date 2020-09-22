<?php



  class ExternalDb {
    
    //This section is changed according to database
    public $fields=array(
        array("fieldname"=>"group_name",
              "tablename"=>"topEmployers",
              "displayname"=>"Top level employer",
              "join"=>"",
              "fieldjoins"=>"",
             ),
        array("fieldname"=>"group_name", 
              "tablename"=>"primaryEmployers",
              "displayname"=>"Division or Employer Name", 
              "join"=>"",
              "fieldjoins"=>""
              ),
        array("fieldname"=>"worksite_name", 
              "tablename"=>"worksites", 
              "displayname"=>"Worksite Code", 
              "join"=>"",
              "fieldjoins"=>"INNER JOIN oms.associations AS worksiteAssociations ON oms.persons.person_id=worksiteAssociations.person_id INNER JOIN oms_employments.employment_worksites ON worksiteAssociations.association_id=employment_worksites.association_id INNER JOIN oms_employments.worksites ON worksites.address_id=employment_worksites.address_id INNER JOIN org.addresses AS worksiteAddresses ON worksites.address_id=worksiteAddresses.address_id"
              ),
        array("fieldname"=>"gender_id", 
              "tablename"=>"persons", 
              "displayname"=>"Gender", 
              "join"=>"",
              "fieldjoins"=>""
              ),
        array("fieldname"=>"group_name", 
              "tablename"=>"committeeGroups", 
              "displayname"=>"Committee Name", 
              "join"=>"committeeAssociations.association_from <= CURRENT_DATE AND (committeeAssociations.association_to > CURRENT_DATE OR committeeAssociations.association_to IS NULL)", 
              "fieldjoins"=>"INNER JOIN oms.associations AS committeeAssociations ON oms.persons.person_id=committeeAssociations.person_id INNER JOIN oms.groups AS committeeGroups ON committeeAssociations.group_id=committeeGroups.group_id INNER JOIN oms_committees.committees ON committeeGroups.group_id=oms_committees.committees.group_id"
              )
    );
    public $starters=array(
        "wheres"=>array(
            /* Exclude people with no email address */
            "(oms.persons.person_email_address != '' AND oms.persons.person_email_address IS NOT NULL)",
            /* Exclude non-current members */
            "membershipAssociations.association_from <= CURRENT_DATE",
            "(membershipAssociations.association_to IS NULL OR membershipAssociations.association_to >= CURRENT_DATE)",
            "membershipGroups.group_name = 'CPSU SPSF Victoria'",
            /* Employer Info */
            "employerAssociations.association_from <= CURRENT_DATE",
            "(employerAssociations.association_to IS NULL OR employerAssociations.association_to >= CURRENT_DATE)",
            /* Exclude noemail keyword */
            "persons.keywords not ilike '%noemail%'",
            /* Exclude no email newsletters committee */
            "oms.persons.person_id NOT IN (SELECT person_id FROM oms.associations INNER JOIN oms.groups ON oms.groups.group_id=oms.associations.group_id INNER JOIN oms_committees.committees ON oms.groups.group_id=oms_committees.committees.group_id WHERE group_name ilike '%No Email Newsletters%' AND association_from <= CURRENT_DATE AND (association_to IS NULL OR association_to >= CURRENT_DATE))"
         ),
        "joins"=>array(/* Membership Group Join */
                       "/*Membership Group Join */",
                       "INNER JOIN oms.associations AS membershipAssociations ON oms.persons.person_id=membershipAssociations.person_id",
                       "INNER JOIN oms.groups AS membershipGroups ON membershipAssociations.group_id=membershipGroups.group_id",
                       "INNER JOIN oms.membership_groups ON membershipGroups.group_id=oms.membership_groups.group_id",
                       "LEFT JOIN oms.membership_associations ON membershipAssociations.association_id=oms.membership_associations.association_id",
                       /* Primary Employer Join */
                       "",
                       "/* Primary Employer Join */",
                       "LEFT JOIN oms.associations AS employerAssociations ON oms.persons.person_id=employerAssociations.person_id",
                       "LEFT JOIN oms.groups AS primaryEmployers ON employerAssociations.group_id=primaryEmployers.group_id",
                       "LEFT JOIN oms_employments.employers AS primaryEmp ON primaryEmp.group_id=primaryEmployers.group_id",
                       /* Parent employer join (if there is one) */
                       "",
                       "/* Parent Employer Join */",
                       "LEFT JOIN oms_employments.employers as topEmp ON primaryEmp.parent_group_id=topEmp.group_id",
                       "LEFT JOIN oms.groups as topEmployers ON topEmp.group_id=topEmployers.group_id",
                       //"INNER JOIN oms.associations AS committeeAssociations ON oms.persons.person_id=committeeAssociations.person_id",
                       //"INNER JOIN oms.groups AS committeeGroups ON committeeAssociations.group_id=committeeGroups.group_id",
                       //"LEFT JOIN oms_committees.committees ON committeeGroups.group_id=oms_committees.committees.group_id"
                       ),
        "froms"=>array("oms.persons"),
        "selects"=>array(
            "distinct on (oms.membership_associations.membership_association_reference) oms.membership_associations.membership_association_reference as member", 
            "oms.persons.person_alias as pref_name", 
            "oms.persons.person_last_name as surname", 
            "oms.persons.person_email_address as email"), 
    );
    
    public $library="oms";
    
    public $sqllike="ilike"; //use a different version of like for different sql instances
    
    private $odbc_dsn="must2";
    private $odbcuser = "sa";
    private $odbcpass = "";
    //Connection string to database
    
    function execute($sql) {
        $dblink = pg_connect("host=192.9.200.16 port=6432 dbname=cpsuvic user=user password=password") or die("OMS Connection Failed");
        //$dblink = pg_connect($this->odbc_dsn, $this->odbcuser, $this->odbcpass);
        @$result= pg_query($dblink, $sql);
        if($result===false) {
            $errormsg=pg_last_error($dblink);
            pg_close($dblink);
            return array("error"=>$errormsg, "data"=>array());
        }
        $output=array();
        while ($row=pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
            $output[]= $row;
        }
        //while (odbc_fetch_row($result)) {
        //    $output[]=odbc_result($result, 1);
        //}
        pg_close($dblink);
        return array("error"=>0, "data"=>$output);
    }
    
    //die("ExternalDb has been loaded");
    function getFields() {
        return $fields;
    }
    
    function getSQL($sql) {
        //$data = execute($sql);;
    }    
}
?>
