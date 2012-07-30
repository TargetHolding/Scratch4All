import os.path
import xml.etree.ElementTree as ET
import pycurl
import cStringIO

importDirPath = 'D:/BOOK/'
xmlFile = 'import.xml'
scanExtentsion = '.jpg'
login_url = 'http://217.21.192.152:8080/catchplus/auth/signIn'
api_url = 'http://217.21.192.152:8080/catchplus/api';


# Acceptance environment
login_url = 'http://workspaces.target-imedia.nl:8080/workspaces/auth/signIn'
api_url = 'http://workspaces.target-imedia.nl:8080/workspaces/api'


username = 'instituut01@admin.nl';
password = 'qwerty';

response = cStringIO.StringIO()
etImport = ET.parse(importDirPath + xmlFile)

# Authentication
c = pycurl.Curl()
c.setopt(pycurl.COOKIEFILE, '')
c.setopt(pycurl.POST, 1)
c.setopt(pycurl.URL, login_url)
c.setopt(pycurl.POSTFIELDS, 'username=' + username + '&password=' + password)
c.perform()

# Get bundle id based on bundle title
c.setopt(pycurl.POST, 0)
c.setopt(pycurl.URL, api_url + '/bundle')
response = cStringIO.StringIO()
c.setopt(pycurl.WRITEFUNCTION, response.write)
c.perform()
etBundles = ET.XML(response.getvalue())
for etBundle in etBundles.findall('bundle'):
    if etBundle.find('title').text == etImport.find('bundle').attrib.get('title'):
        bundleId = etBundle.attrib.get('id')
    
# Iterate book scans
for fileName in os.listdir(importDirPath):
    if os.path.isfile(importDirPath + fileName) and os.path.splitext(fileName)[1] == scanExtentsion:
        # Get meta data for this item
        for etBaseContent in etImport.findall('baseContents/baseContent'):
            if etBaseContent.attrib.get('filename') == fileName:
                metaData = {'description':etBaseContent.attrib.get('description'), 'title':etBaseContent.attrib.get('title')}
                # Make XML for this item
                xml = ('<baseContent>'
                         '<ownerBundle id="' + bundleId + '"/>'
                         '<description>' + metaData['description'] + '</description>'
                         '<title>' + metaData['title'] + '</title>'
                       '</baseContent>')
                # POST XML to CatchPlus REST API (create)
                c.setopt(pycurl.POST, 1)
                c.setopt(pycurl.URL, api_url + "/baseContent")
                c.setopt(pycurl.HTTPPOST, [('xml', xml), ("file", (pycurl.FORM_FILE, importDirPath + fileName))])
                response = cStringIO.StringIO()
                c.setopt(pycurl.WRITEFUNCTION, response.write)
                c.perform()
                print response.getvalue()
        
c.close()