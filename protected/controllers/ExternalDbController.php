<?php
class ExternalDbController extends Controller
{
    function actionIndex() {
          //SEND SQL TO ODBC DATABASE
          
          $sql=isset($_POST['sql']) ? $_POST['sql'] : "SELECT distinct ON (oms.membership_associations.membership_association_reference) oms.membership_associations.membership_association_reference as member, oms.persons.person_alias as pref_name, oms.persons.person_last_name as surname, oms.persons.person_email_address as email

FROM oms.persons

  /** JOIN WITH MEMBERSHIP GROUP **/
  INNER JOIN oms.associations AS membershipAssociations ON oms.persons.person_id=membershipAssociations.person_id
  INNER JOIN oms.groups AS membershipGroups ON membershipAssociations.group_id=membershipGroups.group_id
  INNER JOIN oms.membership_groups ON membershipGroups.group_id=oms.membership_groups.group_id
  LEFT JOIN oms.membership_associations ON membershipAssociations.association_id=oms.membership_associations.association_id

  /** JOIN WITH WORKSITE **/
  INNER JOIN oms.associations AS worksiteAssociations ON oms.persons.person_id=worksiteAssociations.person_id
  INNER JOIN oms_employments.employment_worksites ON worksiteAssociations.association_id=employment_worksites.association_id
  INNER JOIN oms_employments.worksites ON worksites.address_id=employment_worksites.address_id
  INNER JOIN org.addresses AS worksiteAddresses ON worksites.address_id=worksiteAddresses.address_id

  /** JOIN WITH EMPLOYER **/
  INNER JOIN oms.associations AS employerAssociations ON oms.persons.person_id=employerAssociations.person_id
  INNER JOIN oms.groups AS primaryEmployers ON employerAssociations.group_id=primaryEmployers.group_id
  INNER JOIN oms_employments.employers as primaryEmp ON primaryEmp.group_id=primaryEmployers.group_id

  /** JOIN WITH PARENT EMPLOYER (IF THERE IS ONE) **/
  LEFT JOIN oms_employments.employers AS topEmp on primaryEmp.parent_group_id=topEmp.group_id
  LEFT JOIN oms.groups as topEmployers ON topEmp.group_id=topEmployers.group_id

  /** JOIN WITH CLASSIFICATIONS **/
  INNER JOIN oms.associations AS classificationAssociations ON oms.persons.person_id=classificationAssociations.person_id
  INNER JOIN oms_employments.employments ON employments.association_id=classificationAssociations.association_id
  INNER JOIN oms_employments.work_classifications ON employments.work_classification_id=work_classifications.work_classification_id



WHERE persons.person_email_address IS NOT NULL
  AND persons.person_email_address != ''

  /* Exclude non-current members */
  AND membershipAssociations.association_from <= current_date
  AND (membershipAssociations.association_to IS NULL OR membershipAssociations.association_to >= current_date) 
  AND membershipGroups.group_name = 'CPSU SPSF Victoria'

  /* Exclude non-current employers **/
  AND employerAssociations.association_from <= CURRENT_DATE
  AND (employerAssociations.association_to IS NULL OR employerAssociations.association_to >= current_date)

  /* Exclude non-current worksites **/
  AND (employment_worksites.employment_worksite_from <= CURRENT_DATE)
  AND (employment_worksites.employment_worksite_to IS NULL OR employment_worksites.employment_worksite_to >= CURRENT_DATE)
  AND worksiteAssociations.association_from <= CURRENT_DATE
  AND (worksiteAssociations.association_to IS NULL OR worksiteAssociations.association_to >= current_date) 


  /* Exclude non-current employments **/
  AND classificationAssociations.association_from <= current_date
  AND (classificationAssociations.association_to IS NULL OR classificationAssociations.association_to >= current_date)

  /* Exclude people who have \"noemail\" */
  AND persons.keywords not ilike '%noemail%'
  /* Exclude people in the NoEmail committee */
  AND persons.person_id NOT IN (
    SELECT oms.persons.person_id 
    FROM oms.persons 
      INNER JOIN oms.associations AS committeeAssociations on oms.persons.person_id=committeeAssociations.person_id
      INNER JOIN oms.groups as committeeGroups on committeeGroups.group_id=committeeAssociations.group_id
    WHERE group_name = 'No Email Newsletters'
      AND committeeAssociations.association_from <= current_date
      AND (committeeAssociations.association_to >= current_date OR committeeAssociations.association_to IS NULL)
  )"; //Alternative is just for testing purposes
          
          $externaldb=new ExternalDb;
          $return = $externaldb->execute($sql);
          $dataerror = $return['error'];
          $data = $return['data'];
          $count=count($data);
          //RETURN IT IN JSON FORMAT
          $this->renderPartial('index', array(
            'dataerror'=>$dataerror,
            'data'=>$data,
            'count'=>$count,
          ));
    }
    
    function actioncheckValidSql() {
          //SEND SQL TO ODBC DATABASE, and return empty if no error, something (error message) if an error
          
          $sql=isset($_POST['sql']) ? $_POST['sql'] : "SELECT SELECT distinct ON (oms.membership_associations.membership_association_reference) oms.membership_associations.membership_association_reference as member, oms.persons.person_alias as pref_name, oms.persons.person_last_name as surname, oms.persons.person_email_address as email

FROM oms.persons

  /** JOIN WITH MEMBERSHIP GROUP **/
  INNER JOIN oms.associations AS membershipAssociations ON oms.persons.person_id=membershipAssociations.person_id
  INNER JOIN oms.groups AS membershipGroups ON membershipAssociations.group_id=membershipGroups.group_id
  INNER JOIN oms.membership_groups ON membershipGroups.group_id=oms.membership_groups.group_id
  LEFT JOIN oms.membership_associations ON membershipAssociations.association_id=oms.membership_associations.association_id

  /** JOIN WITH WORKSITE **/
  INNER JOIN oms.associations AS worksiteAssociations ON oms.persons.person_id=worksiteAssociations.person_id
  INNER JOIN oms_employments.employment_worksites ON worksiteAssociations.association_id=employment_worksites.association_id
  INNER JOIN oms_employments.worksites ON worksites.address_id=employment_worksites.address_id
  INNER JOIN org.addresses AS worksiteAddresses ON worksites.address_id=worksiteAddresses.address_id

  /** JOIN WITH EMPLOYER **/
  INNER JOIN oms.associations AS employerAssociations ON oms.persons.person_id=employerAssociations.person_id
  INNER JOIN oms.groups AS primaryEmployers ON employerAssociations.group_id=primaryEmployers.group_id
  INNER JOIN oms_employments.employers as primaryEmp ON primaryEmp.group_id=primaryEmployers.group_id

  /** JOIN WITH PARENT EMPLOYER (IF THERE IS ONE) **/
  LEFT JOIN oms_employments.employers AS topEmp on primaryEmp.parent_group_id=topEmp.group_id
  LEFT JOIN oms.groups as topEmployers ON topEmp.group_id=topEmployers.group_id

  /** JOIN WITH CLASSIFICATIONS **/
  INNER JOIN oms.associations AS classificationAssociations ON oms.persons.person_id=classificationAssociations.person_id
  INNER JOIN oms_employments.employments ON employments.association_id=classificationAssociations.association_id
  INNER JOIN oms_employments.work_classifications ON employments.work_classification_id=work_classifications.work_classification_id



WHERE persons.person_email_address IS NOT NULL
  AND persons.person_email_address != ''

  /* Exclude non-current members */
  AND membershipAssociations.association_from <= current_date
  AND (membershipAssociations.association_to IS NULL OR membershipAssociations.association_to >= current_date) 
  AND membershipGroups.group_name = 'CPSU SPSF Victoria'

  /* Exclude non-current employers **/
  AND employerAssociations.association_from <= CURRENT_DATE
  AND (employerAssociations.association_to IS NULL OR employerAssociations.association_to >= current_date)

  /* Exclude non-current worksites **/
  AND (employment_worksites.employment_worksite_from <= CURRENT_DATE)
  AND (employment_worksites.employment_worksite_to IS NULL OR employment_worksites.employment_worksite_to >= CURRENT_DATE)
  AND worksiteAssociations.association_from <= CURRENT_DATE
  AND (worksiteAssociations.association_to IS NULL OR worksiteAssociations.association_to >= current_date) 


  /* Exclude non-current employments **/
  AND classificationAssociations.association_from <= current_date
  AND (classificationAssociations.association_to IS NULL OR classificationAssociations.association_to >= current_date)

  /* Exclude people who have \"noemail\" */
  AND persons.keywords not ilike '%noemail%'
  /* Exclude people in the NoEmail committee */
  AND persons.person_id NOT IN (
    SELECT oms.persons.person_id 
    FROM oms.persons 
      INNER JOIN oms.associations AS committeeAssociations on oms.persons.person_id=committeeAssociations.person_id
      INNER JOIN oms.groups as committeeGroups on committeeGroups.group_id=committeeAssociations.group_id
    WHERE group_name = 'No Email Newsletters'
      AND committeeAssociations.association_from <= current_date
      AND (committeeAssociations.association_to >= current_date OR committeeAssociations.association_to IS NULL)
  )";
          
          
          $externaldb=new ExternalDb;
          $return = $externaldb->execute($sql);
          $dataerror = $return['error'];
          //print_r($dataerror); die();
          $data = $return['data'];
          //RETURN IT IN JSON FORMAT
          $this->renderPartial('checkValidSql', array(
            'dataerror'=>$dataerror,  
          ));
        
    }
    
}  
?>
