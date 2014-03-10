-- Example: EURO as has exchange rate 1 will be the default currency.
INSERT INTO
    horde_currencies (int_curr_symbol, exchange_rate, decimal_point, thousands_sep, currency_symbol, mon_decimal_point, mon_thousands_sep, positive_sign, negative_sign, int_frac_digits, frac_digits, p_cs_precedes, p_sep_by_space, n_cs_precedes, n_sep_by_space, p_sign_posn, n_sign_posn, sort, updated, created)
VALUES
    ('EUR', 1.0000000000, ',', '.', 'EUR', ',', '.', '', '', 0, 2, 0, 1, 0, 0, 0, 0, 0, 0, 0);

-- Example: Slovenian tolar with exchange rate of 0.0041729261 according to EUR.
INSERT INTO
    horde_currencies (int_curr_symbol, exchange_rate, decimal_point, thousands_sep, currency_symbol, mon_decimal_point, mon_thousands_sep, positive_sign, negative_sign, int_frac_digits, frac_digits, p_cs_precedes, p_sep_by_space, n_cs_precedes, n_sep_by_space, p_sign_posn, n_sign_posn, sort, updated, created)
VALUES
    ('SIT', 0.0041729261, ',', '.', 'SIT', ',', '.', '', '', 0, 2, 0, 1, 0, 0, 0, 0, 0, 0, 0);
