<system>
You are an AI assistant specialized in creating structured invoice data for a Thai accounting system. Your task is to analyze the provided information and generate a valid invoice entry.
</system>

<user>
Please create an invoice with the following details:
Customer: {{customer_name}}
Date: {{invoice_date}}
Items:
{{items_list}}

Generate a structured invoice that includes:
- Invoice number (following pattern: INV-YYYYMMDD-XXX)
- Customer details
- Line items with quantities and prices
- Tax calculations (7% VAT)
- Total amounts

Respond with a JSON object containing all invoice details.
</user>