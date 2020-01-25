<?php

namespace Laravolt\SemanticForm\Liveware;

use DeepCopy\DeepCopy;
use Illuminate\Support\Collection;
use Livewire\Component;

class Multirow extends Component
{
    public $name;

    public $schema;

    public $source;

    public $limit;

    public $allowAddition;

    public $allowRemoval;

    public $labels = [];

    protected $counter;

    public function mount($name, $schema, $source, $limit, $allowAddition, $allowRemoval)
    {
        $this->schema = $schema;
        $this->source = $source;
        $this->name = $name;
        $this->limit = $limit;
        $this->allowAddition = $allowAddition;
        $this->allowRemoval = $allowRemoval;
        $this->counter = $this->limit;
        $this->labels = collect($this->schema)->pluck('label')->toArray();
    }

    public function getFieldsProperty()
    {
        return collect(form()->make($this->schema)->all())
            ->transform(function ($item) {
                $item->label(null);

                return $item;
            });
    }

    public function getRowsProperty()
    {
        // reset old values row index
        $data = [$this->name => array_values(old($this->name, $this->source))];

        $rows = [];
        for ($i = 0; $i < $this->counter; $i++) {
            $rows[] = $this->fields->map(function ($field) use ($data, $i) {
                $copier = new DeepCopy();
                $newField = $copier->copy($field);
                $newField->bindAttribute('name', $i);
                $newField->populateValue($data);

                return $newField;
            });
        }

        return $rows;
    }

    public function addRow()
    {
        $this->counter++;
        $this->emit('rowAdded');
    }

    public function removeRow($index)
    {
        $this->emit('rowRemoved');
    }

    public function render()
    {
        return view('semantic-form::livewire.multirow');
    }
}
