<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Util\ContactUtil;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    public function index(Request $request)
    {
        $status = "";
        return view('pages.contacts.index',[
            'title' => "Contacts",
            'status' => $status,
            'contact_types' => ContactUtil::getContactType()
            ]
        );
    }

    public function gcontact(Request $request)
    {
        $status = "";
        return view('pages.gcontacts.index',[
            'title' => "Contacts",
            'status' => $status,
            'contact_types' => ContactUtil::getContactType()
            ]
        );
    }

    public function datatable(Request $request){
        return ContactUtil::datatable($request);
    }

    public function gdatatable(Request $request)
    {
        return ContactUtil::datatable($request, 'global');
    }

    public function edit(Request $request){
        $id = $request->route('id');

        $deals = NULL;
        if ($id != '')
        {
            if($request->method()=='POST'){
                ContactUtil::saveContactOne($id,$request);
            }
            $title='Edit Contact';
            $contact = ContactUtil::contact($id);
            $dealContacts = ContactUtil::deal_contact($id);
            if(!empty($dealContacts)) {
                $deals = array();
                foreach ($dealContacts as $dealContact) {
                    $deals[] =  ContactUtil::deal($dealContact->deal_id);
                }
            }
        }
        else
        {
            $contact = ContactUtil::contact(0);
            $title='Create Contact';
        }

        $contact_types = ContactUtil::getContactType();
        $titles = ContactUtil::getPersonTitle();
        $maritalStatuses = ContactUtil::getMaritalStatus();

        $contactAddress = array();
        if(isset($contact->id)) {
            $contactAddress = ContactUtil::getContactAddress($contact->id);
        }
        if(empty($contactAddress) || $contactAddress->count() == 0) {
            $contactAddress = ContactUtil::getContactAddress(0);
        }

        $contactEmployment = array();
        if(isset($contact->id)) {
            $contactEmployment = ContactUtil::getContactEmployment($contact->id);
        }else{
            $contactEmployment = ContactUtil::getContactEmployment(0);
        }

        $listContact = array();
        if(isset($contact->id)) {
            $listContact = ContactUtil::getContactByUser($contact->id);
        }else $listContact = ContactUtil::getContactByUser(0);

        return view('pages.contacts.form',[
            'contact_types' => $contact_types,
            'title' => $title,
            'titles' => $titles,
            'contact' => $contact,
            'maritalStatuses' => $maritalStatuses,
            'contactAddress' => $contactAddress,
            'contactEmployment' => $contactEmployment,
            'listContact' => $listContact,
            'deals' => $deals,
            ]
        );
    }

    public function gedit(Request $request){
        $id = $request->route('id');

        $deals = NULL;
        if ($id != '')
        {
            if($request->method()=='POST'){
                ContactUtil::saveContactOne($id,$request);
            }
            $title='Edit Contact';
            $contact = ContactUtil::contact($id);
            $dealContacts = ContactUtil::deal_contact($id);
            if(!empty($dealContacts)) {
                $deals = array();
                foreach ($dealContacts as $dealContact) {
                    $deals[] =  ContactUtil::deal($dealContact->deal_id);
                }
            }
        }
        else
        {
            $contact = ContactUtil::contact(0);
            $title='Create Contact';
        }

        $contact_types = ContactUtil::getContactType();
        $titles = ContactUtil::getPersonTitle();
        $maritalStatuses = ContactUtil::getMaritalStatus();

        $contactAddress = array();
        if(isset($contact->id)) {
            $contactAddress = ContactUtil::getContactAddress($contact->id);
        }
        if(empty($contactAddress) || $contactAddress->count() == 0) {
            $contactAddress = ContactUtil::getContactAddress(0);
        }

        $contactEmployment = array();
        if(isset($contact->id)) {
            $contactEmployment = ContactUtil::getContactEmployment($contact->id);
        }else{
            $contactEmployment = ContactUtil::getContactEmployment(0);
        }

        $listContact = array();
        if(isset($contact->id)) {
            $listContact = ContactUtil::getContactByUser($contact->id);
        }else $listContact = ContactUtil::getContactByUser(0);

        return view('pages.gcontacts.form',[
            'contact_types' => $contact_types,
            'title' => $title,
            'titles' => $titles,
            'contact' => $contact,
            'maritalStatuses' => $maritalStatuses,
            'contactAddress' => $contactAddress,
            'contactEmployment' => $contactEmployment,
            'listContact' => $listContact,
            'deals' => $deals,
            ]
        );
    }

    public function autocomplete(Request $request){
        return ContactUtil::autocomplete($request);
    }
    public function create(Request $request){
        $title='Create Contact';
        if($request->method()=='POST'){
            ContactUtil::saveContactOne(0,$request);
            return;
        }
        $contact_types = ContactUtil::getContactType();
        $titles = ContactUtil::getPersonTitle();
        $maritalStatuses = ContactUtil::getMaritalStatus();

        return view('pages.contacts.create',[
            'contact_types' => $contact_types,
            'title' => $title,
            'titles' => $titles,
            'maritalStatuses' => $maritalStatuses,
            ]
        );
    }


    public function gcreate(Request $request){
        $title='Create Contact';
        if($request->method()=='POST'){
            ContactUtil::saveContactOne(0,$request, 'global');
            return;
        }
        $contact_types = ContactUtil::getContactType();
        $titles = ContactUtil::getPersonTitle();
        $maritalStatuses = ContactUtil::getMaritalStatus();

        return view('pages.gcontacts.create',[
            'contact_types' => $contact_types,
            'title' => $title,
            'titles' => $titles,
            'maritalStatuses' => $maritalStatuses,
            ]
        );
    }

    public function delvalidate(Request $request){
        return ContactUtil::delvalidate($request);
    }

    public function delete(Request $request){
        ContactUtil::delete($request->route('id'));
        return redirect('/contact');
    }

    public function gdelete(Request $request){
        ContactUtil::delete($request->route('id'));
        return redirect('/gcontact');
    }
}
