<?php

namespace VentureDrake\LaravelCrm\Http\Livewire;

use Livewire\Component;
use VentureDrake\LaravelCrm\Models\Customer;
use VentureDrake\LaravelCrm\Models\Organization;
use VentureDrake\LaravelCrm\Models\Person;

class LiveLeadForm extends Component
{
    public $client_id;

    public $clientHasOrganizations = false;

    public $clientHasPeople = false;

    public $client_name;

    public $people = [];

    public $person_id;

    public $person_name;

    public $organizations = [];

    public $organization_id;

    public $organization_name;

    public $title;

    public $generateTitle;

    public function mount($lead, $generateTitle = true, $client = null, $organization = null, $person = null)
    {
        $this->client_id = old('client_id') ?? $lead->client->id ?? $client->id ?? null;
        $this->client_name = old('client_name') ?? $lead->client->name ?? $client->name ?? null;
        $this->person_id = old('person_id') ?? $lead->person->id ?? $person->id ?? null;
        $this->person_name = old('person_name') ?? $lead->person->name ?? $person->name ?? null;
        $this->organization_id = old('organization_id') ?? $lead->organization->id ?? $organization->id ?? null;
        $this->organization_name = old('organization_name') ?? $lead->organization->name ?? $organization->name ?? null;

        if ($this->client_id) {
            $this->getClientOrganizations();

            $this->getClientPeople();
        }

        $this->title = old('title') ?? $lead->title ?? null;
        $this->generateTitle = $generateTitle;

        if (old('title') || (isset($lead) && $lead->title)) {
            $this->generateTitle = false;
        } else {
            $this->generateTitle();
        }
    }

    public function updatedClientName($value)
    {
        $this->generateTitle();

        if ($this->client_id) {
            $this->getClientOrganizations();

            $this->getClientPeople();
        } else {
            $this->clientHasOrganizations = false;

            $this->clientHasPeople = false;

            $this->dispatchBrowserEvent('clientNameUpdated');

            if (! $this->organization_id) {
                $this->dispatchBrowserEvent('selectedOrganization');
            }

            if (! $this->person_id) {
                $this->dispatchBrowserEvent('selectedPerson');
            }
        }
    }

    public function updatedOrganizationId($value)
    {
        if ($organization = Organization::find($value)) {
            $address = $organization->getPrimaryAddress();
            $this->dispatchBrowserEvent('selectedOrganization', [
                'id' => $value,
                'address_line1' => $address->line1 ?? null,
                'address_line2' => $address->line2 ?? null,
                'address_line3' => $address->line3 ?? null,
                'address_city' => $address->city ?? null,
                'address_state' => $address->state ?? null,
                'address_code' => $address->code ?? null,
                'address_country' => $address->country ?? null,
            ]);
            $this->organization_name = $organization->name;
        } else {
            $this->dispatchBrowserEvent('selectedOrganization');
        }
    }

    public function updatedOrganizationName($value)
    {
        $this->generateTitle();
    }

    public function updatedPersonId($value)
    {
        if ($person = Person::find($value)) {
            $email = $person->getPrimaryEmail();
            $phone = $person->getPrimaryPhone();
            $this->dispatchBrowserEvent('selectedPerson', [
                'id' => $value,
                'email' => $email->address ?? null,
                'email_type' => $email->type ?? null,
                'phone' => $phone->number ?? null,
                'phone_type' => $phone->type ?? null,
            ]);
        } else {
            $this->dispatchBrowserEvent('selectedPerson');
        }
    }

    public function updatedPersonName($value)
    {
        $this->generateTitle();
    }

    public function generateTitle()
    {
        if ($this->generateTitle) {
            if ($this->client_name) {
                $this->title = $this->client_name.' '.ucfirst(trans('laravel-crm::lang.lead'));
            } elseif ($this->organization_name) {
                $this->title = $this->organization_name.' '.ucfirst(trans('laravel-crm::lang.lead'));
            } elseif ($this->person_name) {
                $this->title = $this->person_name.' '.ucfirst(trans('laravel-crm::lang.lead'));
            }
        }
    }

    public function updatedTitle($value)
    {
        $this->generateTitle = false;
    }

    public function getClientOrganizations()
    {
        foreach (Customer::find($this->client_id)->contacts()
            ->where('entityable_type', 'LIKE', '%Organization%')
            ->get() as $contact) {
            $this->organizations[$contact->entityable_id] = $contact->entityable->name;
            $this->clientHasOrganizations = true;
        }
    }

    public function getClientPeople()
    {
        foreach (Customer::find($this->client_id)->contacts()
            ->where('entityable_type', 'LIKE', '%Person%')
            ->get() as $contact) {
            $this->people[$contact->entityable_id] = $contact->entityable->name;
            $this->clientHasPeople = true;
        }
    }

    public function render()
    {
        return view('laravel-crm::livewire.live-lead-form');
    }
}
