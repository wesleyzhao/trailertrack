'''
Created on Dec 21, 2010

@author: Wesley
'''
import urllib2
import re

def readPage(trailerURL):
    '''Takes in @trailerURL (a web address) and returns the source of that page as a String'''
    contents = urllib2.urlopen(trailerURL)
    source = contents.read()
    contents.close()
    return source

def getTrailerInfo(pageSource):
    '''Takes in @pageSource (String representation of webpage source code
    And returns a list of all tables matching the parser regex'''
    info = re.compile('<table width="700".*?</table>', re.DOTALL)
    rowList = info.findall(pageSource)
    return rowList

def getMovieInfo(pageSource):
    '''Takes in @pageSource (String representation of a webpage source code'''
    '''And returns a Dictionary with these List keys: "Directors", "Writers", "Actors", "Genres"'''
    '''And the Dictionary keys 'Released' {"Day","Month","Year"}'''
    dict = {}
    headerRE = re.compile('<div class="infobar">.*?</div>',re.DOTALL)
    header = (re.search(headerRE,pageSource)).group(0)
    genreRE = re.compile('href="/genre/\w+')
    genreList = [x[(x.rfind('/')+1):] for x in genreRE.findall(header)]   ##my most proud list comprehension :)
    dict['Genres']=genreList
    dateRE = re.compile('>(?P<day>\d+)&nbsp;(?P<month>[A-Za-z]+)&nbsp;(?P<year>\d+).*?(?P<place>.*?)<',re.DOTALL)
    dateMatch = re.search(dateRE, header)
    if dateMatch.group('place'):
        dict['Released']={'Day':dateMatch.group('day'),'Month':dateMatch.group('month'),'Year':dateMatch.group('year')}
    else:
        dict['Released']={}
    infoRE = re.compile('div class="txt-block">(?P<directors>.*?</div>.*?)(?P<writers><div.*?</div>.*?)(?P<stars><div.*?</div>)',re.DOTALL)
    infoMatch = re.search(infoRE,pageSource)
    def getPeople(text):
        peopleRE = re.compile('>[^<]+</a')
        peopleList = [x[(x.find('>')+1):x.find('<')] for x in peopleRE.findall(text) if not ' more ' in x]  ##my most proudest, actually!
        return peopleList
    dirBlock = infoMatch.group('directors')
    writBlock = infoMatch.group('writers')
    starBlock = infoMatch.group('stars')
    dict['Directors']=getPeople(dirBlock)
    dict['Writers']=getPeople(writBlock)
    dict['Actors']=getPeople(starBlock)
    return dict

def scrapeInfo(dataList):
    '''Take in @dataList (a list of all table elements with trailers on trailerfreaks.com
    Returns a dictionary with the Movie Title as the key, and the key values being: 
    "PosterSrc","IMDBrefer","TrailerSrc", and "ClipType"'''
    findReg = re.compile('<tr>.*?alt="(?P<movieTitle>[^"]*)".*?<tr>.*?img src.*?"(?P<imgSrc>[^"]*)".*?<tr>.*?a href="http.*?imdb.*?title.(?P<IMDBrefer>\w+).".*?(<tr>.*?){4}alt="(?P<clipType>[^"]*)".*?<tr>.*?a href="(?P<trailerLink>[^"]*)"',re.DOTALL)
    ##labels: movieTitle,imgSrc,IMDBrefer,trailerLink
    dict = {}
    for table in dataList:
        ##matched = re.match(findReg,table)
        matched = findReg.search(table)
        if matched:
            movieDict = {'PosterSrc':matched.group('imgSrc'),'IMDBrefer':matched.group('IMDBrefer'),'TrailerSrc':matched.group('trailerLink'), 'ClipType':matched.group('clipType')}
            dict[matched.group('movieTitle')]=movieDict
    return dict

def getPosters(preURL, linklist):
    '''Takes in @preURL (the root URL) and @linklist (a list of source URL to the posters
    and download the Posters to a given folder in root directory'''
    folder = "posters"
    for link in linklist:
        webFile = urllib2.urlopen(preURL+'/'+link)
        localFile = open(folder+'/'+link.split('/')[-1],'w')
        localFile.write(webFile.read())
        webFile.close()
        localFile.close()

def getClipsDict(preURL, linkDict):
    '''Takes in @preURL (the root URL) and @linkDict (a dictionary of movie keys to the source clip URL as key-values
    and downloads the movie clips (as .mov) into a given folder in root directory with the name of the key'''
    folder = "trailers"
    for key in linkDict.keys():
        webFile = urllib2.urlopen(preURL+linkDict[key])
        #localFile = open(folder+'/'+key+'.mov','wb')
        localFile = open(folder+'/'+key,'wb')
        localFile.write(webFile.read())
        webFile.close()
        localFile.close()
        
def scrapeIMDB(indexList):
    '''Reads in @indexList (a list of imdb Refer numbers)
    And returns a dictionary with the imdbRefer # as the key, and a dictionary with the
    info as a key-value (refer to getMovieInfo)'''
    dict = {}
    for index in indexList:
        dict[index]=getMovieInfo(readPage('http://www.imdb.com/title/'+index))
    return dict

def indexMap(dict):
    '''Takes in a @dict (dictionary with Movie Titles as the key and dictionary (with 'IMDBrefer' as a key) as a
    key-value. Returns a dictionary with the IMDBrefer as the keys, and the Movie Titles as the key-value '''
    indexDict={}
    for key in dict.keys():
        indexDict[dict[key]['IMDBrefer']]=key
    return indexDict


##comment out for DATALOAD.py
'''
#scrapeInfo test
"""input = open("source","r")
inputText = input.read()
input.close()
outputList = getTrailerInfo(inputText)"""
outputList=getTrailerInfo(readPage('http://trailerfreaks.com'))
myDict = scrapeInfo(outputList)
imdbScrape = scrapeIMDB(indexMap(myDict))
imdbOut = open('IMDBoutput','w')
imdbOut.write(str(imdbScrape))
imdbOut.close()
print 'done'
##outputFile2 = open('scrapeOutput','w')
##outputFile2.write(str(myDict))
##outputFile2.close()
postList = []
trailList = []
trailDict={}
postList=[]
for key in myDict.keys():
    postList.append(myDict[key]['PosterSrc'])
getPosters('http://trailerfreaks.com',postList)
for key in myDict.keys():
    trailDict[key]=myDict[key]['TrailerSrc']
    ##trailList.append(myDict[key]['TrailerSrc'])
##print trailList
##newTrail = [trailList[0],trailList[1],trailList[2]]
getClipsDict('',trailDict)
'''#comment out for DataLoad.py