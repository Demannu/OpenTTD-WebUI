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


# USAGE: ofs-transfer <id> <savegame-to-transfer>

# Directory where ofs-transfer should look for the file named in the 2nd parameter
localdir = './save/'
# Should contain the user@host of the server you are transferring the game to.
# Leave set to 'local' if the bot has local write-access to the destination without resorting to ssh
host = 'local'
# Ssh port to use. No need to touch unless you use ssh and its on a non-standard port
port = 22
# destination directory. If this resides on a different machine/user, make sure
# to set the ssh-parameters above. avoid using ~ for local, use absolute path instead
destdir = '/home/openttd/public_html/'
# Resulting filename. use {ID} where you want the identifier to end up in the filename.
savename = '{ID}_Final.sav'



# -------------------- DO NOT EDIT ANYTHING BELOW THIS LINE --------------------

import os, os.path
import pipes
from shutil import copy #file
from subprocess import Popen, PIPE, CalledProcessError
import sys

def main(localdir, host, port, destdir, savename):
    ReturnValues = assignReturnValues()
    # set current working directory to wherever ofs-svnupdate is located in case of relative paths
    os.chdir(os.path.dirname(os.path.abspath(__file__)))

    if len(sys.argv) <= 2:
        print 'Error: Not enough parameters'
        sys.exit(ReturnValues.get('BADPARAMETERS'))
    else:
        gameID = sys.argv[1]
        sourcegame = sys.argv[2]

    savename = savename.replace('{ID}', gameID)
    localfile = os.path.join(localdir, sourcegame)
    if not os.path.isfile(localfile):
        print 'Could not locate local file %s. Please check for typo\'s' % localfile
        sys.exit(ReturnValues.get('NOSOURCE'))

    destfile = os.path.join(destdir, savename)
    if host == 'local':
        fileExists = os.path.isfile(destfile)
    else:
        sshCommand = 'ssh -p%d %s test -f %s' % (port, host, pipes.quote(destfile))
        returnCode = executeTest(sshCommand)
        if returnCode == 255:
            print 'Error: SSH returned errorcode 255, check your configuration' % (
                sshCommand[0], commandObject.returncode, sshCommand[0], output)
            sys.exit(ReturnValues.get('BADCONFIG'))
        fileExists = (returnCode == 0)
    if fileExists:
        print 'Destination file %s already exists. Exiting without transfer' % destfile
        sys.exit(ReturnValues.get('FILEEXISTS'))

    if host == 'local':
        try:
            copy(os.path.abspath(localfile), os.path.abspath(destfile))
        except IOError as e:
            print 'Something went wrong whilst copying the file: %s' % e.strerror
            sys.exit(ReturnValues.get('FAILED'))
        print '%s succesfully saved' % savename
        sys.exit(ReturnValues.get('SUCCESS'))
    else:
        destination = '%s:%s' % (host, destfile)
        command = 'scp -P%d %s %s' % (port, localfile, destination)
        if not execute(command, shell=True):
            print 'Something went wrong whilst copying the file'
            sys.exit(ReturnValues.get('FAILED'))
        else:
            print '%s succesfully saved' % savename
            sys.exit(ReturnValues.get('SUCCESS'))

def execute(command, shell = False):
    print 'Executing: "%s"' % command
    if not shell:
        command = command.split()
    try:
        commandObject = Popen(command, shell=shell, stdout = PIPE)
    except OSError as e:
        print 'Error: Could not execute. Please check %s is installed and working' % command.split()[0]
        return False
    output = commandObject.stdout.read()
    commandObject.stdout.close()
    commandObject.wait()
    if commandObject.returncode:
        print 'Error: %s sys.exited with status %s\n%s output:\n%s' % (command.split()[0], commandObject.returncode, command.split()[0], output)
        return False
    else:
        return True

def executeTest(command):
    print 'Executing: "%s"' % command
    try:
        commandObject = Popen(command, shell=True)
    except OSError as e:
        print 'Error: Could not execute. Please check %s is installed and working' % command.split()[0]
        return False
    commandObject.wait()
    return commandObject.returncode

def assignReturnValues():
    values = {
        'SUCCESS'           : 0x00, # Game transferred succesfully
        'FILEEXISTS'        : 0x01, # File already exists in destination, not overwriting
        'BADPARAMETERS'     : 0x02, # Either ID or savegame parameters are missing, or both
        'BADCONFIG'         : 0x03, # The ssh-related variables are not set up properly
        'NOSOURCE'          : 0x04, # Couldn't read the source file to transfer
        'FAILED'            : 0x05, # Failed to copy the file
    }
    return values

if __name__ == '__main__':
    main(localdir, host, port, destdir, savename)
