-- 
-- Table structure for table horde_currencies.
--
-- $Horde: incubator/Horde_Currencies/currencies.sql,v 1.4 2007/05/04 15:18:05 duck Exp $
-- 

CREATE TABLE horde_currencies (
  int_curr_symbol CHAR(3) NOT NULL,
  exchange_rate FLOAT NOT NULL,
  decimal_point CHAR(1),
  thousands_sep CHAR(1),
  currency_symbol VARCHAR(3),
  mon_decimal_point CHAR(1),
  mon_thousands_sep CHAR(1),
  positive_sign CHAR(1),
  negative_sign CHAR(1),
  int_frac_digits NUMERIC(3),
  frac_digits NUMERIC(3),
  p_cs_precedes NUMERIC(1),
  p_sep_by_space NUMERIC(1),
  n_cs_precedes NUMERIC(1),
  n_sep_by_space NUMERIC(1),
  p_sign_posn NUMERIC(1),
  n_sign_posn NUMERIC(1),
  sort INT,
  created INT,
  updated INT,
--
  PRIMARY KEY  (int_curr_symbol)
);
