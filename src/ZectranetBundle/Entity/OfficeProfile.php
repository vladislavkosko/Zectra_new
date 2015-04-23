<?php


namespace ZectranetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use ZectranetBundle\Entity\Office;

/**
 * OfficeProfile
 *
 * @ORM\Table(name="office_profiles")
 * @ORM\Entity
 */
class OfficeProfile
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="business_name", type="string", length=255, nullable=true, options={"default"=null})
     */
    private $businessName;

    /**
     * @var string
     * @ORM\Column(name="website_url", type="string", length=255, nullable=true, options={"default"=null})
     */
    private $webSiteURL;

    /**
     * @var string
     * @ORM\Column(name="industry", type="string", length=255, nullable=true, options={"default"=null})
     */
    private $industry;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=1000, nullable=true, options={"default"=null})
     */
    private $description;


    private $employeeTitles;


    private $skillsAvailable;

    /**
     * @var int
     * @ORM\Column(name="office_id", type="integer")
     */
    private $officeID;

    /**
     * @var Office
     * @ORM\OneToOne(targetEntity="Office", inversedBy="profile")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     */
    private $office;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set businessName
     *
     * @param string $businessName
     * @return OfficeProfile
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;
    
        return $this;
    }

    /**
     * Get businessName
     *
     * @return string 
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * Set webSiteURL
     *
     * @param string $webSiteURL
     * @return OfficeProfile
     */
    public function setWebSiteURL($webSiteURL)
    {
        $this->webSiteURL = $webSiteURL;
    
        return $this;
    }

    /**
     * Get webSiteURL
     *
     * @return string 
     */
    public function getWebSiteURL()
    {
        return $this->webSiteURL;
    }

    /**
     * Set industry
     *
     * @param string $industry
     * @return OfficeProfile
     */
    public function setIndustry($industry)
    {
        $this->industry = $industry;
    
        return $this;
    }

    /**
     * Get industry
     *
     * @return string 
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return OfficeProfile
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set officeID
     *
     * @param integer $officeID
     * @return OfficeProfile
     */
    public function setOfficeID($officeID)
    {
        $this->officeID = $officeID;
    
        return $this;
    }

    /**
     * Get officeID
     *
     * @return integer 
     */
    public function getOfficeID()
    {
        return $this->officeID;
    }

    /**
     * Set office
     *
     * @param \ZectranetBundle\Entity\Office $office
     * @return OfficeProfile
     */
    public function setOffice(\ZectranetBundle\Entity\Office $office = null)
    {
        $this->office = $office;
    
        return $this;
    }

    /**
     * Get office
     *
     * @return \ZectranetBundle\Entity\Office 
     */
    public function getOffice()
    {
        return $this->office;
    }
}
