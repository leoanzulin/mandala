/// <reference types="cypress" />

context('Actions', () => {
  beforeEach(() => {
    cy.visit('http://localhost:12345');
  });

  // https://on.cypress.io/interacting-with-elements

  it('.type() - type into a DOM element', () => {
    // https://on.cypress.io/type
    cy.get('#LoginForm_username')
      .type('fake@email.com').should('have.value', 'fake@email.com');
    cy.get('#LoginForm_password')
      .type('asdfqwerzxcv').should('have.value', 'asdfqwerzxcv');
    cy.get('[type=submit]')
      .click();
  });
});
