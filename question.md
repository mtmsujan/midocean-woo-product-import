# Missing fields on api

1. [Radio button labels](https://prnt.sc/8Bz0sQJtuosg)
2. [Print configuration color](https://prnt.sc/3H_F-vIKJapc)

## Print Order Response

```JSON
{
    "order_header": {
        "status_code": "0",
        "status_text": "Sales order successfully created",
        "direct_confirmation": "",
        "check_price": "false",
        "currency": "EUR",
        "po_number": "your order reference",
        "order_type": "PRINT",
        "order_number": "3217473",
        "total_item_price": "114,00",
        "total_print_costs": "99,00",
        "total_net_price": "237,95",
        "freight_charge": "24.950000000 ",
        "tax": "0,00",
        "print_setup": "30,00",
        "print_cost": "67,00",
        "print_handling": "2,00",
        "shipping_address": {
            "contact_name": "Contact name",
            "company_name": "Company name",
            "street1": "Street",
            "street2": "",
            "street3": "",
            "postal_code": "Postal code",
            "city": "City",
            "region": "",
            "country": "NL",
            "email": "contact e-mail",
            "phone": "phone"
        }
    },
    "order_lines": [
        {
            "status_code": "0",
            "status_text": "",
            "po_line_id": "1",
            "sku": "",
            "variant_id": "",
            "quantity": "100",
            "expected_price": "0",
            "master_code": "AR1804",
            "master_id": "40000190",
            "shipping_date": "2024-09-05",
            "printing_positions": [
                {
                    "id": "FRONT",
                    "print_size_height": "190",
                    "print_size_width": "120",
                    "printing_technique_id": "S2",
                    "number_of_print_colors": "1",
                    "print_colors": [
                        {
                            "color": "Pantone 4280C"
                        }
                    ],
                    "print_artwork_url": "your logo URL",
                    "print_mockup_url": "your mockup URL",
                    "print_instruction": "Test Order"
                }
            ],
            "print_items": [
                {
                    "item_color_number": "03",
                    "item_size": "",
                    "quantity": "100"
                }
            ]
        }
    ]
}
```

## Order Details

```JSON

{
    "order_header": {
        "order_found": "true",
        "order_number": "3217473",
        "order_date": "2024-09-03",
        "order_status": "OPEN",
        "sales_org": "0101",
        "currency": "EUR",
        "customer_number": "80881610",
        "contact_person": "Universo Merchan SL",
        "po_number": "your order reference",
        "order_type": "PRINT",
        "shipping_address": {
            "company_name": "Company name",
            "contact_name": "Contact name",
            "street1": "Street",
            "street2": "",
            "postal_code": "Postal cod",
            "city": "City",
            "region": "",
            "country": "NL",
            "email": "contact e-mail",
            "phone": "PHONE"
        },
        "total_item_price": "114.0",
        "discounts": "0.0",
        "total_print_costs": "99.0",
        "freight_charge": "24.95",
        "total_net_price": "237.95",
        "tax": "0.0",
        "total_gross_price": "237.95",
        "incoterms": "DAP",
        "delivery_service": "STANDARD"
    },
    "order_lines": [
        {
            "order_line_id": "100",
            "master_code": "AR1804",
            "master_id": "40000190",
            "quantity": "1",
            "item_price": "0.0",
            "print_setup": "30.0",
            "print_cost": "67.0",
            "print_handling": "2.0",
            "proof_url": "",
            "proof_status": "ArtworkRequired",
            "shipping_status": "OPEN",
            "shipping_date": "2024-09-05",
            "print_items": [
                {
                    "sku": "AR1804-03",
                    "variant_id": "10168709",
                    "quantity": "100",
                    "item_price": "1.14"
                }
            ],
            "printing_positions": [
                {
                    "id": "FRONT",
                    "print_size_height": "190",
                    "print_size_width": "120",
                    "printing_technique_id": "S2",
                    "number_of_print_colors": "1"
                }
            ]
        }
    ]
}

```
