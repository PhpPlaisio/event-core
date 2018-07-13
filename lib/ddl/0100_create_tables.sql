/*================================================================================*/
/* DDL SCRIPT                                                                     */
/*================================================================================*/
/*  Title    :                                                                    */
/*  FileName : abc-event-core.ecm                                                 */
/*  Platform : MySQL 5.6                                                          */
/*  Version  : Concept                                                            */
/*  Date     : vrijdag 13 juli 2018                                               */
/*================================================================================*/
/*================================================================================*/
/* CREATE TABLES                                                                  */
/*================================================================================*/

CREATE TABLE ABC_EVENT (
  aev_id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL,
  aev_label VARCHAR(40) NOT NULL,
  CONSTRAINT PK_ABC_EVENT PRIMARY KEY (aev_id)
);

CREATE TABLE ABC_EVENT_LISTENER (
  ael_id INT UNSIGNED AUTO_INCREMENT NOT NULL,
  cmp_id SMALLINT UNSIGNED,
  aev_id SMALLINT UNSIGNED NOT NULL,
  ael_class VARCHAR(128) CHARACTER SET ascii NOT NULL,
  ael_weight SMALLINT NOT NULL,
  CONSTRAINT PK_ABC_EVENT_LISTENER PRIMARY KEY (ael_id)
);

/*
COMMENT ON COLUMN ABC_EVENT_LISTENER.ael_weight
The weight of the listener. The lightest listener will be executed first.
*/

/*================================================================================*/
/* CREATE INDEXES                                                                 */
/*================================================================================*/

CREATE UNIQUE INDEX IX_ABC_EVENT_LISTENER1 ON ABC_EVENT_LISTENER (aev_id, ael_class);

/*================================================================================*/
/* CREATE FOREIGN KEYS                                                            */
/*================================================================================*/

ALTER TABLE ABC_EVENT_LISTENER
  ADD CONSTRAINT FK_ABC_EVENT_LISTENER_ABC_AUTH_COMPANY
  FOREIGN KEY (cmp_id) REFERENCES ABC_AUTH_COMPANY (cmp_id);

ALTER TABLE ABC_EVENT_LISTENER
  ADD CONSTRAINT FK_ABC_EVENT_STEP_ABC_EVENT
  FOREIGN KEY (aev_id) REFERENCES ABC_EVENT (aev_id);
