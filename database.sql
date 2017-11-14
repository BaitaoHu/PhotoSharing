/** Uncomment to reset database
DROP TABLE Photo;
DROP TABLE TextPost;
DROP TABLE Response;
DROP TABLE Post;
DROP TABLE Album;
DROP TABLE ProUser;
DROP TABLE NormalUser;
COMMIT;
*/

CREATE TABLE NormalUser 
(username varchar(20), 
     pass varchar(20) NOT NULL,             -- Passwords 20 characters max
     email varchar(50) NOT NULL UNIQUE,     -- Email address 50 characters max
     birthday date, 
     joindate date NOT NULL,
     PRIMARY KEY (username));


CREATE TABLE ProUser 
(username varchar(20), 
     membershipExpiryDate date NOT NULL,
     signature varchar(50),                                     -- Signature 50 characters max
     profileURL varchar(200) NOT NULL,      --  URL 200 characters max
     PRIMARY KEY (username),
     FOREIGN KEY (username) REFERENCES NormalUser(username) ON DELETE CASCADE);

CREATE TABLE Album 
(albId int,
     name varchar(50) NOT NULL,               -- Album name 50 characters max
     username varchar(20) NOT NULL,
     PRIMARY KEY (albId),
     FOREIGN KEY (username) REFERENCES ProUser(username) ON DELETE CASCADE);
 
CREATE TABLE Post 
(postId int,
     createdAt date NOT NULL, 
     username varchar(20) NOT NULL,                     -- Username 20 characters max
     likes int CHECK (likes >= 0), 
     PRIMARY KEY (postId),
     FOREIGN KEY (username) REFERENCES NormalUser(username) ON DELETE CASCADE);


CREATE TABLE Photo 
(postId int,
     URL varchar(200), 
     description varchar(500), 
     height int NOT NULL CHECK (height >= 0), -- I just removed the size limit for now
     width int NOT NULL CHECK (width >= 0), 
     albId int NOT NULL,
     PRIMARY KEY (postId),
     FOREIGN KEY (postId) REFERENCES Post(postId)  ON DELETE CASCADE,
     FOREIGN KEY (albId) REFERENCES Album(albId) ON DELETE CASCADE);



CREATE TABLE TextPost 
(postId int, 
    contents varchar(500) NOT NULL,          -- TextPost post 500 characters max
    PRIMARY KEY (postId),
    FOREIGN KEY (postId) REFERENCES Post(postId)  ON DELETE CASCADE);


CREATE TABLE Response 
(postId integer, 
     content varchar(200) NOT NULL,
     datePosted date,
     username varchar(20),
     PRIMARY KEY (postId, datePosted, username),
     FOREIGN KEY (postId) REFERENCES Post(postId) ON DELETE CASCADE,
     FOREIGN KEY (username) REFERENCES NormalUser(username) ON DELETE CASCADE);

Insert into NormalUser
values('Alvin','123456789','AlvinHu@gmail.com', TO_DATE('1996-10-18', 'YYYY-MM-DD'), TO_DATE('2017-10-11', 'YYYY-MM-DD'));

Insert into NormalUser
values('Michael','987654321','MichaelZhang@gmail.com', TO_DATE('1995-1-1', 'YYYY-MM-DD'), TO_DATE('2009-10-28', 'YYYY-MM-DD'));

Insert into NormalUser
values('Katherina','qwertyuiop','KatherinaShen@gmail.com', TO_DATE('2000-10-11', 'YYYY-MM-DD'), TO_DATE('2017-10-18', 'YYYY-MM-DD'));

Insert into NormalUser
values('Dominic','poiuytrewq','DominicKuang@gmail.com', TO_DATE('1994-4-5', 'YYYY-MM-DD'), TO_DATE('2016-4-2', 'YYYY-MM-DD'));

Insert into NormalUser
values('JaneDoe','123456789','1234566@gmail.com', NULL,TO_DATE('2002-8-8', 'YYYY-MM-DD'));

Insert into NormalUser
values('John','55443322','John@gmail.com', TO_DATE('2005-1-26', 'YYYY-MM-DD'), TO_DATE('2012-5-8', 'YYYY-MM-DD'));

Insert into ProUser
values('Alvin',TO_DATE('2018-10-23', 'YYYY-MM-DD'),'I am the first member', 'www.google.com');

Insert into ProUser
values('Michael',TO_DATE('2019-10-23', 'YYYY-MM-DD'),NULL, 'www.ubc.ca');

Insert into ProUser
values('Katherina',TO_DATE('2018-2-25', 'YYYY-MM-DD'),'I am lalalalala', 'www.baidu.com');

Insert into Album
values(0001,'UBC','Alvin');

Insert into Album
values(4631,'SFU','Alvin');

Insert into Album
values(5631,'KPU','Alvin');

Insert into Album
values(0971,'Vancouver','Michael');

Insert into Album
values(1234,'Toronto','Michael');

Insert into Album
values(4321,'Canada','Katherina');

Insert into Album
values(5656,'USA','Katherina');

Insert into Post
values(010203,TO_DATE('2017-5-4 12:05:04', 'YYYY-MM-DD HH24:MI:SS'),'Alvin',5);

Insert into Post
values(016443,TO_DATE('2017-8-1 23:55:24', 'YYYY-MM-DD HH24:MI:SS'),'Michael',0);

Insert into Post
values(546453,TO_DATE('2015-1-1 03:22:34', 'YYYY-MM-DD HH24:MI:SS'),'Alvin',3);

Insert into Post
values(123123,TO_DATE('2016-5-4 12:55:01', 'YYYY-MM-DD HH24:MI:SS'),'Katherina',43);

Insert into Post
values(342633,TO_DATE('2017-7-2 18:25:01', 'YYYY-MM-DD HH24:MI:SS'),'Alvin',5);

Insert into Post
values(857465,TO_DATE('2016-4-21 15:05:33', 'YYYY-MM-DD HH24:MI:SS'),'Michael',2);

Insert into Post
values(423433,TO_DATE('2017-3-2 12:35:55', 'YYYY-MM-DD HH24:MI:SS'),'Alvin',3);

Insert into Post
values(5786878,TO_DATE('2017-8-31 12:35:55', 'YYYY-MM-DD HH24:MI:SS'),'Alvin',1);

Insert into Post
values(234323,TO_DATE('2017-5-31 12:35:55', 'YYYY-MM-DD HH24:MI:SS'),'Alvin',1);

Insert into Post
values(34585656,TO_DATE('2017-8-31 12:35:55', 'YYYY-MM-DD HH24:MI:SS'),'Michael',5);

Insert into Post
values(5674645,TO_DATE('2017-9-30 12:10:55', 'YYYY-MM-DD HH24:MI:SS'),'Michael',2);

Insert into Post
values(87789,TO_DATE('2017-8-31 5:35:55', 'YYYY-MM-DD HH24:MI:SS'),'Katherina',7);

Insert into Post
values(2345754,TO_DATE('2017-8-31 7:44:11', 'YYYY-MM-DD HH24:MI:SS'),'Katherina',9);

Insert into Post
values(978947,TO_DATE('2017-5-20 12:35:08', 'YYYY-MM-DD HH24:MI:SS'),'Dominic',13);

Insert into Post
values(345345,TO_DATE('2017-2-17 15:39:02', 'YYYY-MM-DD HH24:MI:SS'),'Dominic',1);

Insert into Photo
values(010203,'https://source.unsplash.com/Ovn1hyBge38','Aurora',4912,7360,0001);

Insert into Photo
values(016443,'https://source.unsplash.com/kFHz9Xh3PPU','Yosemite',3720,5580,0971);

Insert into Photo
values(546453,'https://source.unsplash.com/Sm08kLSX4fU','Car',4000,6000,5631);

Insert into Photo
values(123123,'https://source.unsplash.com/mTkKYzlAAMc','Penguin',2848,4288,4321);

Insert into Photo
values(342633,'https://source.unsplash.com/T5lmpSYxnSU','Man',2504,4067,0001);

Insert into Photo
values(857465,'https://source.unsplash.com/XAqaeyzj3NM','Brooklyn',4417,6626,1234);

Insert into Photo
values(234323,'https://source.unsplash.com/d1fDmShBtIk','Girl',3456,5184,5656);

Insert into TextPost
values(5786878,'aaaaaaaaaaaaaaaaaaaaaaaaaaa');

Insert into TextPost
values(34585656,'sssssssssssssssssssssssssssss');

Insert into TextPost
values(5674645,'yyyyyyyyyyyyyyyyyyyyyyyyyyyy');

Insert into TextPost
values(87789,'rrrrrrrrrrrrggggggggggg');

Insert into TextPost
values(2345754,'sssssssssssssssssssssggggggg');

Insert into TextPost
values(978947,'hhhhhhhhhhhhhhyyyyyyyy');

Insert into TextPost
values(345345,'ccccccccccccccccqqqqqqqqqqqq');

Insert into Response
values(345345,'Aurora is so beautiful!', TO_DATE('2017-5-6 14:05:04', 'YYYY-MM-DD HH24:MI:SS'),'Dominic');

Insert into Response
values(010203,'Where is this place?',TO_DATE('2017-5-4 14:15:03', 'YYYY-MM-DD HH24:MI:SS'),'John');

Insert into Response
values(010203,'I want to go there!',TO_DATE('2017-5-4 14:16:22', 'YYYY-MM-DD HH24:MI:SS'),'John');

Insert into Response
values(016443,'It is really a nice picture',TO_DATE('2017-8-2 04:55:04', 'YYYY-MM-DD HH24:MI:SS'),'Dominic');

Insert into Response
values(87789,'I like it',TO_DATE('2017-1-1 15:22:14', 'YYYY-MM-DD HH24:MI:SS'),'Alvin');

Insert into Response
values(87789,'Thank you!',TO_DATE('2017-1-1 15:25:14', 'YYYY-MM-DD HH24:MI:SS'),'Katherina');
