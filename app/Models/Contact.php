<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    public function type()
    {
        return $this->belongsTo('App\Models\ContactType','contacttype_id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
    public function title()
    {
        return $this->belongsTo('App\Models\PersonTitle','persontitle_id');
    }
    public $streetTypes = [
        'Avenue','Court','Drive','Place','Road','Street','Access','Alley','Alleyway','Amble','Anchorage','Approach','Arcade','Artery','Bank','Basin','Beach','Bend','Block','Boulevard','Bowl','Brace','Brae','Break','Bridge','Broadway','Brow','Bypass','Byway','Causeway','Centre','Centreway','Chase','Circle','Circlet','Circuit','Circus','Close','Colonnade','Common','Concourse','Copse','Corner','Corso','Courtyard','Cove','Crescent','Crest','Cross','Crossing','Crossroad','Crossway','Cruiseway','Cul-de-sac','Cutting','Dale','Dell','Deviation','Dip','Distributor','Driveway','Edge','Elbow','End','Entrance','Esplanade','Estate','Expressway','Extension','Fairway','FireTrack','Flat','Follow','Footway','Foreshore','Formation','Freeway','Front','Frontage','Gap','Garden','Gardens','Gate','Gates','Glade','Glen','Grange','Green','Ground','Grove','Gully','Heights','Highroad','Highway','Hill','Interchange','Intersection','Junction','Key','Landing','Lane','Laneway','Lees','Line','Link','Little','Lookout','Loop','Lower','Mall','Meander','Mew','Mews','Mile','Motorway','Mount','Nook','Outlook','Parade','Park','Parklands','Parkway','Part','Pass','Path','Pathway','Piazza','Pier','Plateau','Plaza','Pocket','Point','Port','Promenade','Quad','Quadrangle','Quadrant','Quay','Quays','Ramble','Ramp','Range','Reach','Reserve','Rest','Retreat','Ride','Ridge','Ridgeway','RightofWay','Ring','Rise','River','Riverway','Riviera','Roadeside','Roadway','Ronde','Rosebowl','Rotary','Round','Route','Row','Rue','Run','ServiceWay','Siding','Slope','Sound','Spur','Square','Stairs','StateHighway','Steps','Strand','Strip','Subway','Tarn','TarniceWay','Terrace','Thoroughfare','Tollway','Top','Tor','Towers','Track','Trail','Trailer','Triangle','Trunkway','Turn','Underpass','Upper'
    ];
    public $addressOwnerShip = [
        'Boarding','boarding with parents','owned','jointly owned','rent free','rent free with parfents','rented by applicant','to be purchased'
    ];
    public $addressStatus = [
        'Current','post settlement','previous','postal'
    ];
    public $addressState = [
        'Victoria','New Sout Wales','Queensland','South Australia','Western Australia','Tasmania','Northern Territory','Australian Capital Territory'
    ];
    public $employmentCategory = [
        'PAYG','Self Employed - Company','Self Employed - Partnership','Self Employed - Sole Trader','Hoome Duties','Pensioner','Retired','Student','Unemployed'
    ];
    public $employmentStatus = [
        'Full Time','Part Time','Casual','Contract','Commission Based','Seasonal','Temporary'
    ];
}
