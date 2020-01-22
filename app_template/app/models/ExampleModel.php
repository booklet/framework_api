<?php
class ExampleModel extends Model
{
    public function fields()
    {
        return [
            'id' => ['type' => 'integer'],
            'name' => ['type' => 'string', 'validations' => ['required', 'max_length:190']],
            'name_search' => ['type' => 'string', 'validations' => ['required', 'max_length:190']],
            'status' => ['type' => 'datetime', 'default' => 'new'],
            'created_at' => ['type' => 'datetime'],
            'updated_at' => ['type' => 'datetime'],
        ];
    }

    // Relacje między obiektami:
    // has_many - utworzy metodę podaną jako klucz tablicy np.: $object->contacts();
    // belongs_to - utworzy metodę podaną jako klucz tablicy np.: $object->client();
    // has_and_belongs_to_many - utworzy dwie metody:
    // $client->categoriesPush($category1);
    // $client->categoriesDelete($category1);
    // has_and_belongs_to_many - ma i prznależy do wielu - wymaga tabeli łączącej
    // o nazwie modeli które ma połączyć, w kolejnosci alfabetycznej np.:
    // modele: clients, categories
    // nazwa tabeli łączącej => categories_clients
    // bez kolumny id, ale z dwoma kolumnami: category_id i client_id.
    //   $data = ['client' => [
    //     'name' => 'Client name',
    //     'categories_ids' => ['1', '2']
    //   ]];
    public static function relations()
    {
        return [
            'contacts' => ['relation' => 'has_many', 'class' => 'Contact'],
            'emails' => ['relation' => 'has_many', 'class' => 'ClientEmail'],
            'client' => ['relation' => 'belongs_to', 'class' => 'Client'],
            'categories' => ['relation' => 'has_and_belongs_to_many', 'class' => 'ClientCategory'],
        ];
    }

    // "nested atributes" pozwalają zapisac na sapisywanie obiektu wraz
    // z modelami podanymi w relations.
    // Mając model clienta który na wiele emaili możemy zapisać taki obiekt:
    // $client = [
    //   'name' => 'Nazwa',
    //   'emails_attributes' => [
    //      ['adress' => 'test1@test.com'],
    //      ['adress' => 'test2@test.com']
    //    ]
    // ];
    // Wywołując metodę save(), zapiszemy nowego klienta oraz adresy email,
    // id rodzica (client_id w modelu Email) zostanie automatycznie ustawione.
    // Przykład wykorzystania:
    // Tworzenie nowego klienta przez API, standardowo trzeba by utworzyć klienta
    // a następnie po kolei tworzyć emaile, w sumie 3 zapytania do API,
    // przesyłając dane nowego klienta jak podano powyżej możemy to zrobić jednym
    // zapytaniem do API.
    // Działa to tylko dla relacji 'has_many'.
    public function acceptsNestedAtributesFor()
    {
        return ['emails', 'contacts'];
    }

    // funkcja zostanie wywoływana przed validacją obiektu
    public function beforeValidate()
    {
        // to simplify search text: '„UTASZ-SPEED” Sp. z o.o.' to: 'utaszspeedspzoo'
        // use in search query this same transliterate method
        $this->name_search = StringUntils::transliterate($this->name);
    }

    // Model pozwala na uzywanie tylko tych atrubutów które zostały zdefiniowane
    // poprzez np.: 'public $name;', (zgodnie z załozeniem deklarujemy tylko
    // te atrybutu które pokrywją się z bazą danych).
    // Jednak może się zdarzyć że bedziemy potrzebować atrybutu który nie jest
    // zdeklarowany, możmy się wtedy posłuzyć specialPropertis, które pozwala
    // na definiowanie wirtualnych atrybutów, zostaną pominięte przy zapisie
    // w bazie danych.
    public function specialPropertis()
    {
        return ['categories'];
    }
}
