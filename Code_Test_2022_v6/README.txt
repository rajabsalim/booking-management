I wanted to bring your attention to the changes I made in the bookingController.php and some functions in the bookingRepository, 
but I had to limit the extent of the changes due to time constraints. I would have to love to apply the change to the whole bookingRepository file
too but was only able to refractor it to this function "sendNotificationTranslator" Overall, I believe these changes have made the code more maintainable
and easier to understand

While I have removed unused commented code and fixed variable naming conventions from the whole Repository file, there were still several other changes that I wanted to make but couldn't.
However, I hope we can discuss these further during my next interview.

In terms of the code, there are some conventions that were followed, and some that were not. Here are some of my thoughts on the code:

- The Repository pattern is a great way to separate data access logic from business logic, making the code easier to maintain and test.

- Another layer of service could have been used, but it's a trade-off between better readability and adding an additional overhead to manage files,
 which can make the codebase more complex for new developers.

- Variable naming conventions were not followed, but I tried to fix most of the variable conventions.

- Some functions have "code smells" the reasons are code contained long conditionals, duplicated code, unused variables,
 inconsistent naming conventions, and overly complex nested loops. Some functions were to long, the Strategy pattern could have been used to make these functions more modular.

- There are fewer comments in the code for readability. I wasn't able to add proper comments due to time constraints, but I hope you understand.

- More coding principles like KISS or SOLID could have been applied, but it's not always practical or necessary to follow them.

- Validations were not implemented, but separate validation classes could be created to improve the codebase. It's better to separate validation from the controller.

- The use of exception handling and error reporting could be improved to provide more meaningful feedback to the user and make the code more robust.

- Some of the function names could be improved to better reflect their purpose, making the code easier to understand and navigate.

- I could have used modularization and organization, such as breaking down larger functions into smaller ones or splitting the codebase into more logical components.

