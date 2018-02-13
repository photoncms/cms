<?php

namespace Photon\PhotonCms\Core\Entities\Field;

use Photon\PhotonCms\Core\Entities\Field\Field;

class FieldUpdateComplexitySwitch
{
    public static function determineFieldUpdateComplexity($oldField, $newField)
    {
        $updateModel = true;
        $updateMigration = true;

        return [$updateModel, $updateMigration];
    }

    public static function determineModulesForUpdate($module, $fieldsForUpdate)
    {
        // Na osnovu imena polja treba (samo onih koji se strukturalno menjaju - uglavnom kod brisanja polja)
        // treba utvrditi da li ce update uticati na jos modula i shodno tome backup-ovati i update-ovati i njih



        // if has one to many -> full rebuild
        // if has many to one -> regular rebuild
        // if has many to many -> rebuild source and target module

        // Brisanje modula
        // one to many -> moduli povezani sa ovim imaju povezane vrednosti u svojoj tabeli. Te vrednosti treba obrisati.
                        // Kaskadne veze treba izbegavati. Trenutno nema smisla da postoje pa je dovoljno obrisati
                        // ovaj modul i relaciju iz drugog modula ka njemu.
        // many to one -> Ovde je dovoljno obrisati samo ovaj modul.

        // many to many -> potrebno je obrisati ovaj modul, pivot tabelu i relaciju iz drugog modula ka ovome.

        // Update modula
        // Dozvoljen je samo update polja koji ne utice na postojanje kolone, tj sve sto ide kroz Laravel change.
        // Sledece stavke igraju ulogu u update-u modula: kreiranje novih polja, strukturalni update polja kroz
        // Laravelov change, brisanje polja (zajedno sa relacijama) i update anchor teksta.
        // Pri kreiranju novih polja, potrebno je rebuild-ovati model i napraviti zasebnu migraciju za kreiranje
        // ovog polja.
        // Pri strukturalnom update-ovanju polja potrebno je napraviti migraciju koja ce da izvrsi izmene na polju
        // preko Laravel change.
        // Pri brisanju polja potrebno je takodje proveriti ima li relacija ka polju i obrisati i njih prema
        // principima za brisanje modula.
        // Pri update-ovanju anchor teksta, potrebno je izvrsiti proveru anchor teksta nad stanjem nakon update-a.

        // Brisanje polja
        // Oslanja se na principe brisanja modula i brisanja polja kod update-ovanja modula.
    }
}