UPDATE `Clauses` SET `Text` = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`Text`,'<H2>','<H1>'),'</H2>','</H1>'),'<H3>','<H2>'),'</H3>','</H2>'),'<H4>','<H3>'),'</H4>','</H3>');