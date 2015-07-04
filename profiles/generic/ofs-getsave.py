#!/usr/bin/env python2

###
# This file is part of Ottd File scripts (OFS).
#
# OFS is free software; you can redistribute it and/or modify it under the
# terms of the GNU General Public License as published by the Free Software
# Foundation, version 2.
#
# OFS is distributed in the hope that it will be useful, but WITHOUT ANY
# WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
# A PARTICULAR PURPOSE.
#
# See the GNU General Public License for more details. You should have received
# a copy of the GNU General Public License along with OFS. If not, see
# <http://www.gnu.org/licenses/>.
###



# Where all the savegames go.
savedir = './save'



# -------------------- DO NOT EDIT ANYTHING BELOW THIS LINE --------------------

import os, os.path
import sys
from urllib2 import urlopen, HTTPError, URLError

def main():
    ReturnValues = assignReturnValues()
    # set current working directory to wherever ofs-getsave is located in case of relative paths
    os.chdir(os.path.dirname(os.path.abspath(__file__)))

    if len(sys.argv) <= 1:
        print 'Error: No URL supplied.'
        sys.exit(ReturnValues.get('BADURL'))
    else:
        saveUrl = sys.argv[1]

    if not os.path.isdir(savedir):
        print 'Error: Savedir "%s" is invalid.' % savedir
        sys.exit(ReturnValues.get('INVALIDSAVEDIR'))

    savegame = downloadFile(saveUrl, savedir)
    if isinstance(savegame, tuple):
        print 'Error: Encountered error %s while downloading %s. File not saved' % (savegame[0], savegame[1])
        sys.exit(ReturnValues.get('BADURL'))
    elif not os.path.isfile(savegame):
        print 'Error: File downloaded succesfully, but file was not written. Please check your permissions on %s' % savedir
        sys.exit(ReturnValues.get('DOWNLOADFAILED'))

    print 'File downloaded succesfully. File saved as %s' % savegame
    sys.exit(ReturnValues.get('SUCCESS'))

def downloadFile(url, directory):
    try:
        savefile = os.path.join(directory, os.path.basename(url))
        game = urlopen(url)
        with open(savefile, 'wb') as local_file:
            local_file.write(game.read())
    except HTTPError, e:
        return (e.code, url)
    except URLError, e:
        return (e.reason, url)
    except IOError:
        return 'couldn\'t write file'
    return savefile

def assignReturnValues():
    values = {
        'SUCCESS'         : 0x00, # Program finished successfully
        'INVALIDSAVEDIR'  : 0x01, # Savedir is not a valid or existing directory
        'DOWNLOADFAILED'  : 0x02, # Failed to write downloaded file to disk
        'BADURL'          : 0x03, # Download failed due to bad url (eg, 404, not an actual url)
    }
    return values

if __name__ == '__main__':
    main()
