CREATE TABLE Games (
	GameID		TEXT	PRIMARY KEY,
	Name		TEXT	NOT NULL,
	Passcode	TEXT	NOT NULL,
	Secret		TEXT	NOT NULL
);

CREATE TABLE Characters (
	CharacterID	TEXT	PRIMARY KEY,
	GameID		TEXT	REFERENCES Games(GameID),
	Name		TEXT	NOT NULL,
	IsMaster	BOOLEAN	NOT NULL,
	Secret		TEXT	NOT NULL
);

CREATE TABLE Rolls (
	RollID		TEXT	PRIMARY KEY,
	GameID		TEXT	REFERENCES Games(GameID),
	CharacterID	TEXT	REFERENCES Characters(CharacterID),
	RollString	TEXT	NOT NULL,
	RollResult	TEXT	NOT NULL,
	RollDetails	TEXT	NOT NULL
);
