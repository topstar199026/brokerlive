<ul class="contact-list">
@if(isset($contacts))
    @foreach ($contacts as $key => $contact)
    <li>
        <div class="contact">
            <a href="/dealContact/edit/{{$contact->id}}" class="link-edit">
                <i class="fa fa-user"></i>

                <span class="contact-id">#{{$contact->contact_id}}</span>
                <strong>{{$contact->fullName()}}</strong>
                <span class="contact-type">({{$contact->type->name}})</span>
                <span class="contact-phone">{{$contact->contactNum()}}</span>
            </a>
            <span class="contact-delete">
                <a href="/dealContact/delete/{{$contact->id}}" class="link-delete" title="Delete">
                    <i class="fa fa-trash-o"></i>
                </a>
            </span>
        </div>
        <div class="form-edit" style="display:none;">
            @include('pages.deal.contact.form', ['contactId' => $contact->id])
        </div>
    </li>
    @endforeach
@endif
</ul>
