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



# Where the openttd executable is located
gamedir = '/usr/games/'
# Where all the auto-saves go.
autosavedir = './save/'
# The game gets run with -Dfgc. Add any additional parameters here
parameters = ''



# -------------------- DO NOT EDIT ANYTHING BELOW THIS LINE --------------------

from subprocess import Popen, PIPE, CalledProcessError
import sys
import os, os.path
datadir = os.path.join('/var/www/public_html/ottd/profiles/', sys.argv[2])
def main():
    ReturnValues = assignReturnValues()
    # set current working directory to wherever ofs-svntobin is located in case of relative paths
    os.chdir(os.path.dirname(os.path.abspath(__file__)))
    savegame = sys.argv[1]
    executable = os.path.join(gamedir, 'openttd')
    pidfile = os.path.join(datadir, 'openttd.pid')
    #status = checkStatus(pidfile, executable)
    #if status:
    #    exit(ReturnValues.get('SERVERRUNNING'))

    serverconfig = os.path.join(datadir, 'openttd.cfg')
    lastsave = getLatestAutoSave(autosavedir)

    command = '%s -D -f -c %s -g %s' % (executable, serverconfig, savegame)
    if parameters:
        command += ' %s' % parameters

    ottdOutput = execute(command)
    if not ottdOutput:
        exit(ReturnValues.get('FAILEXECUTE'))

    pid = None
    for line in ottdOutput.splitlines():
        print 'OpenTTD output: %s' % line
        if 'Forked to background with pid' in line:
            words = line.split()
            pid = words[6]
            try:
                with open(pidfile, 'w') as pf:
                    pf.write(str(pid))
            except NameError as e:
                print 'Couldn\'t write to pidfile: %s' % e
                exit(ReturnValues.get('SUCCESSNOPIDFILE'))
            exit(ReturnValues.get('SUCCESS'))
    exit(ReturnValues.get('FAILNOPIDFOUND'))

def checkStatus(pidfile, executable):
    try:
        with open(pidfile) as pf:
            pid = pf.readline()
    except IOError:
        return False
    exename = os.path.basename(executable)
    psOutput = execute('ps -A', shell = True)
    if not psOutput:
        print 'Couldn\'t run ps -A'
        return False
    else:
        for line in psOutput.splitlines():
            if not line == '' and not line == None:
                fields = line.split()
                pspid = fields[0]
                pspname = fields[3]
                if pspid == pid and pspname == exename:
                    print 'OpenTTD found running at pid: %s' % pspid
                    return True
        else:
            return False
        print 'OpenTTD is not running'
        return False

def execute(command, shell = False):
    print 'Executing: "%s"' % command
    if not shell:
        command = command.split()
    try:
        commandObject = Popen(command, shell=shell, stdout = PIPE)
    except OSError as e:
        print 'Could not execute. Please check %s is installed and working' % command.split()[0]
        return False
    output = commandObject.stdout.read()
    commandObject.stdout.close()
    commandObject.wait()
    if commandObject.returncode:
        print '%s exited with status %s\n%s output:\n%s' % (command.split()[0], commandObject.returncode, command.split()[0], output)
        return False
    else:
        return output

def getLatestAutoSave(autosavedir):
    max_mtime = 0
    save = None
    for fname in os.listdir(autosavedir):
        if fname.startswith('autosave'):
            fullpath = os.path.join(autosavedir, fname)
            mtime = os.stat(fullpath).st_mtime
            if mtime > max_mtime:
                max_mtime = mtime
                save = fullpath
    return save

def assignReturnValues():
    values = {
        'SUCCESS'           : 0x00, # OpenTTD started succesfully, pid written to openttd.pid
        'SERVERRUNNING'     : 0x01, # Game is already running, no point starting another instance
        'SUCCESSNOPIDFILE'  : 0x02, # Openttd started succesfully, but could not write to openttd.pid
        'FAILEXECUTE'       : 0x03, # Couldn't run the command
        'FAILNOPIDFOUND'    : 0x04, # No pid found in OpenTTD output, OpenTTD probably didn't start correctly
    }
    return values

if __name__ == '__main__':
    main()
