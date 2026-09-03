# StorageWebsite

Storage is a CRUD application for product management. Allows users to create, view, update and delete products from a database.

---

# Tech Stack

- **Frontend**: HTML, CSS
- **Backend**: PHP with PDO for secure database interaction
- **Database**: MySql

---
# Database connection

Database connection parameters can be configured in `config/db.php`.

# Security & Database

- **Prepared Statement via PDO**: To ensure robust defense against SQL injection attacks, PDO with prepared statements are used for every single database query that processes user data or parameters.
- **URL Parameter Whitelisting**: Strict whitelist validation is implemented for sorting and filtering parameters passed via URL. Any unrecognized parameter is safely discarded and replaced with default values before building queries.
- **Client &Server-side Validation**: Double-layered input validation: Javascript on the client (function `validationInputJS(e)` from `main.js`) , PHP on the server (function `validationInput()` from `validation_input.php`) ensures data integrity and sanitization before processing.
- **Syntax Breakage Prevention**: Product data is safely passed to JavaScript modal handlers using HTML `data-` atributes (`Delete` and `Edit` button from `index.php`), avoiding direct inline script injection and preventing code syntax breakage caused by special characters.

---

# Sorting, Filtering and Search

- **Sorting**: Includes **ASC** and **DESC** sorting buttons for both price and product name.
- **Filtering**: Provides filter option to display either _All_ products or only items in _Stock_.
- **Search bar**: Dynamically filters and displays products whose names contain the searched keyword.

Sorting (ASC/DESC) for name or price and filtering parameters (all/stock) are sent to the PHP backend directly through the URL structure ( `?sort=...&filter=...` )

---

# Features

- **Create**: Add new products to the database.
- **Read**: Display a list with all available products.
- **Update**: Edit existing product details.
- **Delete**: Remove products from the database.

The **Add**, **Edit** and **Delete** action all share the same modal interface, which is dynamically populated and modified by the **crudModal()** JavaScript function.
To pass the product data of the selected item to JavaScript, custom HTML `data-` atributes are attached directly to the respective buttons. This approach was chosen to safely handle special characters and prevent code syntax breakage.

### VIEW

Each product from the database is displayed using a **CSS grid layout** in a table, featuring its own dedicated Edit and Delete buttons.

### Page Number

After sorting and filtering, products are paginated to display 10 items per page, accompanied with page number indicators and navigation buttons.

### Create

To add a new product click the **Add Product** button from the header and fill the required fields marked with a red (\*)

**Validation Rules:**

All the fields are required.

- **Name** Must be between 3-50 characters long.
- **Price** Must contain numbers only.
- **Description** Must be between 3-2000 characters long.
- **Date** Must be a valid date .
- **Image** Accepts only JPEG, PNG formats (maximum 2MB).
- **Stock** Set to _OFF_ ( Is not in stock) by default.

All of these validation are performed both in **JavaScript** (to provide the user a custom error message directly : function `validationInputJS(e)` from `main.js`) as well as at the PHP level as a security mesure. Location :(`crud/validation_input.php`)

When the user submits the form, the function `validationInputJS(e)` retrieves all input data, trim white spaces, and performs the necessary validation checks.

### Client & Server -Side Validation

- **Client-Side Validation Failed**: If errors are found `e.preventDefault()` hold form submission,displaying error messages directly in the Add or Edit modal below the image container.
- **Server-Side Validation Failed**: If client-side validation _passed_ but server-side validation detects an _error_, a generic error message is displayed to the user.
- **Validation Passed**: If no errors are detected, on either client or server side, the database queries are executed securely using **PDO**.


### Edit

To edit a product, click its coresponding _Edit_ button, which opens the shared modal pre-filled with the curent product details. Any modified fields must comply with the same validation rule and the same error message.

### Delete

To delete a product, follow the same step by clicking its corresponding _Delete_ button to open de modal. Server-side validation is not required for this action, as only the product _ID_ is sent.

# Responsive design

The layout adapts across all devices sizes:

- **Mobile**: Products are stacked vertically as individual cards (1 card per row)
- **Tablet**: Displayed in a 2-column card grid.
- **Desktop** (up to 1920px): Renders as a full-width data table.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details,
