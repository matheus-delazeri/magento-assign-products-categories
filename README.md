# Magento 1.9 Assign categories to products
A Magento 1.9's module to assign categories to products through SKU and the categories IDs.

## Module informations
`Package/Namespace`: "Matheus"  

`Modulename`: "AssignCategories"

`codepool`: "community"  

## How to install
Add the folder `Matheus` inside `/app/code/community/` and add the file `Matheus_AssignCategories.xml` inside `/app/etc/modules/`

## How to use
After installation a new submenu named `Assign Categories` will be created at the menu `Catalog` in your admin panel. Click in it to enter the module's page. 


Now, you just need to upload your file and choose between `append` the new categories to the old ones or `replace` the old categories for the new ones.

## Append X Replace
`Append new categories`: this option will assign the new categories without unassign the olds ones that were already assigned to that product.


`Replace existing categories`: this option will unassign the old categories from the product, leaving only the ones in the uploaded file assigned.

## Input file pattern
The input file must be in CSV format and in the following order:
|sku|categories_id|
| --- | --- |
|product-sku|3|
|product-2-sku|2,10,7|

*Products with more than one category must have their categories ids separeted by a comma.
