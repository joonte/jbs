
INSERT INTO `Tasks` (
`ID` ,
`CreateDate` ,
`UserID` ,
`TypeID` ,
`ExecuteDate` ,
`Params` ,
`Errors` ,
`Result` ,
`IsExecuted` ,
`IsActive` 
)
VALUES (
'80', UNIX_TIMESTAMP(), '1', 'DomainsOrdersRegStatusUpdate', UNIX_TIMESTAMP(), '[]' , '0', NULL , 'no', 'yes'
);


