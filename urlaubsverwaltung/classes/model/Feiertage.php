<?php
namespace classes\model;

class Feiertage {
    private $currentNeujahr;
    private $currentKarfreitag;
    private $currentOstermontag;
    private $currentTagDerArbeit;
    private $currentChristiHimmelfahrt;
    private $currentPfingstmontag;
    private $currentTagDerDeutschenEinheit;
    private $currentReformationstag;
    private $currentWeihnachtsfeiertag1;
    private $currentWeihnachtsfeiertag2;
    private $arrAlleCurrentFeiertage;

    private $nextNeujahr;
    private $nextKarfreitag;
    private $nextOstermontag;
    private $nextTagDerArbeit;
    private $nextChristiHimmelfahrt;
    private $nextPfingstmontag;
    private $nextTagDerDeutschenEinheit;
    private $nextReformationstag;
    private $nextWeihnachtsfeiertag1;
    private $nextWeihnachtsfeiertag2;
    private $arrAlleNextFeiertage;


    public function __construct() {
        $this->currentNeujahr                   = mktime(0,0,0,1,1);
        $this->currentKarfreitag                = easter_date() - 2*24*60*60;
        $this->currentOstermontag               = easter_date() + 24*60*60;
        $this->currentTagDerArbeit              = mktime(0,0,0,5,1);
        $this->currentChristiHimmelfahrt        = easter_date() + 39*24*60*60;
        $this->currentPfingstmontag             = easter_date() + 50*24*60*60;
        $this->currentTagDerDeutschenEinheit    = mktime(0,0,0,10,3);
        $this->currentReformationstag           = mktime(0,0,0,10,31);
        $this->currentWeihnachtsfeiertag1       = mktime(0,0,0,12,25);
        $this->currentWeihnachtsfeiertag2       = mktime(0,0,0,12,26);

        $this->arrAlleCurrentFeiertage['Neujahr']                   = $this->currentNeujahr;
        $this->arrAlleCurrentFeiertage['Karfreitag']                = $this->currentKarfreitag;
        $this->arrAlleCurrentFeiertage['Ostermontag']               = $this->currentOstermontag;
        $this->arrAlleCurrentFeiertage['Tag Der Arbeit']            = $this->currentTagDerArbeit;
        $this->arrAlleCurrentFeiertage['Christi Himmelfahrt']       = $this->currentChristiHimmelfahrt;
        $this->arrAlleCurrentFeiertage['Pfingstmontag']             = $this->currentPfingstmontag;
        $this->arrAlleCurrentFeiertage['Tag der deutschen Einheit'] = $this->currentTagDerDeutschenEinheit;
        $this->arrAlleCurrentFeiertage['Reformationstag']           = $this->currentReformationstag;
        $this->arrAlleCurrentFeiertage['1. Weihnachtsfeiertag']     = $this->currentWeihnachtsfeiertag1;
        $this->arrAlleCurrentFeiertage['2. Weihnachtsfeiertag']     = $this->currentWeihnachtsfeiertag2;

        $this->nextNeujahr                   = mktime(0,0,0,1,1,date('Y')+1);
        $this->nextKarfreitag                = easter_date(date('Y')+1) - 2*24*60*60;
        $this->nextOstermontag               = easter_date(date('Y')+1) + 24*60*60;
        $this->nextTagDerArbeit              = mktime(0,0,0,5,1,date('Y')+1);
        $this->nextChristiHimmelfahrt        = easter_date(date('Y')+1) + 39*24*60*60;
        $this->nextPfingstmontag             = easter_date(date('Y')+1) + 50*24*60*60;
        $this->nextTagDerDeutschenEinheit    = mktime(0,0,0,10,3,date('Y')+1);
        $this->nextReformationstag           = mktime(0,0,0,10,31,date('Y')+1);
        $this->nextWeihnachtsfeiertag1       = mktime(0,0,0,12,25,date('Y')+1);
        $this->nextWeihnachtsfeiertag2       = mktime(0,0,0,12,26,date('Y')+1);

        $this->arrAlleNextFeiertage['Neujahr']                   = $this->nextNeujahr;
        $this->arrAlleNextFeiertage['Karfreitag']                = $this->nextKarfreitag;
        $this->arrAlleNextFeiertage['Ostermontag']               = $this->nextOstermontag;
        $this->arrAlleNextFeiertage['Tag Der Arbeit']            = $this->nextTagDerArbeit;
        $this->arrAlleNextFeiertage['Christi Himmelfahrt']       = $this->nextChristiHimmelfahrt;
        $this->arrAlleNextFeiertage['Pfingstmontag']             = $this->nextPfingstmontag;
        $this->arrAlleNextFeiertage['Tag der deutschen Einheit'] = $this->nextTagDerDeutschenEinheit;
        $this->arrAlleNextFeiertage['Reformationstag']           = $this->nextReformationstag;
        $this->arrAlleNextFeiertage['1. Weihnachtsfeiertag']     = $this->nextWeihnachtsfeiertag1;
        $this->arrAlleNextFeiertage['2. Weihnachtsfeiertag']     = $this->nextWeihnachtsfeiertag2;
    }


    public function getCurrentNeujahr()
    {
        return $this->currentNeujahr;
    }

    public function setCurrentNeujahr($currentNeujahr)
    {
        $this->currentNeujahr = $currentNeujahr;
    }

    public function getCurrentKarfreitag()
    {
        return $this->currentKarfreitag;
    }

    public function setCurrentKarfreitag($currentKarfreitag)
    {
        $this->currentKarfreitag = $currentKarfreitag;
    }

    public function getCurrentOstermontag()
    {
        return $this->currentOstermontag;
    }

    public function setCurrentOstermontag($currentOstermontag)
    {
        $this->currentOstermontag = $currentOstermontag;
    }

    public function getCurrentTagDerArbeit()
    {
        return $this->currentTagDerArbeit;
    }

    public function setCurrentTagDerArbeit($currentTagDerArbeit)
    {
        $this->currentTagDerArbeit = $currentTagDerArbeit;
    }

    public function getCurrentChristiHimmelfahrt()
    {
        return $this->currentChristiHimmelfahrt;
    }

    public function setCurrentChristiHimmelfahrt($currentChristiHimmelfahrt)
    {
        $this->currentChristiHimmelfahrt = $currentChristiHimmelfahrt;
    }

    public function getCurrentPfingstmontag()
    {
        return $this->currentPfingstmontag;
    }

    public function setCurrentPfingstmontag($currentPfingstmontag)
    {
        $this->currentPfingstmontag = $currentPfingstmontag;
    }

    public function getCurrentTagDerDeutschenEinheit()
    {
        return $this->currentTagDerDeutschenEinheit;
    }

    public function setCurrentTagDerDeutschenEinheit($currentTagDerDeutschenEinheit)
    {
        $this->currentTagDerDeutschenEinheit = $currentTagDerDeutschenEinheit;
    }

    public function getCurrentReformationstag()
    {
        return $this->currentReformationstag;
    }

    public function setCurrentReformationstag($currentReformationstag)
    {
        $this->currentReformationstag = $currentReformationstag;
    }

    public function getCurrentWeihnachtsfeiertag1()
    {
        return $this->currentWeihnachtsfeiertag1;
    }

    public function setCurrentWeihnachtsfeiertag1($currentWeihnachtsfeiertag1)
    {
        $this->currentWeihnachtsfeiertag1 = $currentWeihnachtsfeiertag1;
    }

    public function getCurrentWeihnachtsfeiertag2()
    {
        return $this->currentWeihnachtsfeiertag2;
    }

    public function setCurrentWeihnachtsfeiertag2($currentWeihnachtsfeiertag2)
    {
        $this->currentWeihnachtsfeiertag2 = $currentWeihnachtsfeiertag2;
    }

    public function getArrAlleCurrentFeiertage()
    {
        return $this->arrAlleCurrentFeiertage;
    }

    public function setArrAlleCurrentFeiertage($arrAlleCurrentFeiertage)
    {
        $this->arrAlleCurrentFeiertage = $arrAlleCurrentFeiertage;

        return $this;
    }

    public function getArrAlleNextFeiertage()
    {
        return $this->arrAlleNextFeiertage;
    }

    public function setArrAlleNextFeiertage($arrAlleNextFeiertage)
    {
        $this->arrAlleNextFeiertage = $arrAlleNextFeiertage;

        return $this;
    }
}