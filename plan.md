## Tasks Queue

- Display Dynamically Radio button labels [Screenshot](https://prnt.sc/N4SBIZz-bGV1)
- Calculate Price [Reference Site Screenshot](https://prnt.sc/fRc90nW3z3CX)

## Solution

**Radio Button Labels** The radio button labels are stored on `Print feed JSON` api endpoint under `printing_technique_descriptions` field. Connecting with `printing_technique_descriptions.id = db table wp_sync_products_print_data.printing_techniques.id` if max colors is 0 sub labels will display full color.

**Calculation**

- Price item (quantity: 1) = product price * quantity
- Peninsula web portes (office 20 eur) = shipping cost **same for all products based on shipping area**
- Printing position 1: 1 color = 30.90€ **before . like 30 this value is `Print Pricelist JSON.setup` value after . like .90 this value is `Print Pricelist JSON.scales.price` value based on minimum quantity**
- Cost manipulation = 0.07 * quantity
- **Total (incl. Transportation)** = Printing position 1: 1 color + Cost manipulation + Peninsula web portes (office 20 eur) + Price item (quantity: 1)
