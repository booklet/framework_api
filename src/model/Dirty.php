<?php
abstract class Dirty
{
    // dodanie method do modelu

    // person.name = 'Bob'
    // person.changed?       # => true
    // person.name_changed?  # => true
    // person.name_changed?(from: nil, to: "Bob") # => true
    // person.name_was       # => nil
    // person.name_change    # => [nil, "Bob"]
    // person.name = 'Bill'
    // person.name_change    # => [nil, "Bill"]
}
