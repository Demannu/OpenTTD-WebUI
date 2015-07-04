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



# directory where you keep the svn checkout. This should be identical to the one
# in ofs-svnupdate.py
sourcedir = '../'
# directory where you keep the running server. Ideally this is where you keep your
# ofs-scripts. Ofs-svnupdate will copy the sourcedir/bundle contents over to this
# directory.
gamedir = './'



# -------------------- DO NOT EDIT ANYTHING BELOW THIS LINE --------------------

from distutils.dir_util import copy_tree, DistutilsFileError
import os, os.path
from sys import exit

def main():
    ReturnValues = assignReturnValues()
    # set current working directory to wherever ofs-svntobin is located in case of relative paths
    os.chdir(os.path.dirname(os.path.abspath(__file__)))

    sourcefiles = os.path.join(sourcedir, 'bundle/')
    try:
        copy_tree(sourcefiles, gamedir, verbose=1)
    except DistutilsFileError, e:
        print str(e)
        exit(ReturnValues.get('FAILURE'))

def assignReturnValues():
    values = {
        'SUCCESS'           : 0x00, # Files copied succesfully
        'FAILURE'           : 0x01, # Something went wrong, see output for details
    }
    return values

if __name__ == '__main__':
    main()
