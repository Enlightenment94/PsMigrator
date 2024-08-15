###PsMigrator (core)

Core tworzone pod migracje pojedyńczych produktów w prestashop.

Funkcjonalność do napisania i wytestowania:

1. Migracja produktu.
    + baza (+)
    + cechy (+/-)
    + kategorie (+)
    + ceny (dostosowywać trzeba przeliczanie przy różnicy głównych walut) (+)
    + tagi (+)
    - dostawcy (-)
    - producenci (-)
2. Sprawdzenie czy struktury bazy są odpowiednie do migracji.
3. Inicjowanie tworzenia produktu przez klasy Prestashop. (+)
4. Inicjowanie tworzenia produktu przez Api. (-) 
5. Automatyzacja migracji.
6. Checker brakujących produktów.
7. Test Migracji całej bazy i intergralności zainstalowanych modułów po migracji.

## Lista testów

1. **Migracja 1 produktu**  
   Opis: Test migracji pojedynczego produktu.

2. **Migracja 10 produktów**  
   Opis: Test migracji dziesięciu produktów.

3. **Migracja 100 produktów**  
   Opis: Test migracji stu produktów.

4. **Migracja wszystkich produktów**  
   Opis: Test migracji wszystkich dostępnych produktów.

5. **Sprawdzenie integralności wtyczek**  
   Opis: Test sprawdzający integralność zainstalowanych wtyczek.