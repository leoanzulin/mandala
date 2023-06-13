DROP TABLE IF EXISTS pagamento;

CREATE TABLE pagamento(
    id BIGSERIAL,
    valor DECIMAL NOT NULL,
    parcela INTEGER NOT NULL,
    inscricao_id BIGINT NOT NULL,
    CONSTRAINT pagamento_pk PRIMARY KEY(id)
);
COMMENT ON TABLE pagamento IS 'Armazena pagamentos de parcelas de alunos.';

ALTER TABLE pagamento ADD CONSTRAINT pagamento_inscricao_fk
    FOREIGN KEY (inscricao_id)
    REFERENCES inscricao (id)
    ON DELETE NO ACTION ON UPDATE NO ACTION
    NOT DEFERRABLE;
